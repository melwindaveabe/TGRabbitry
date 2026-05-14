<template>
    <GuestLayout>
        <ImageBlg
            title="Our Products"
            desc="Did you know that rabbits sometimes binky or dance when they are really happy? A binky is when they jump into the air and twist around!"
            image="Baby.jpg"
        />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

            <!-- Category filter tabs -->
            <div class="flex items-center gap-2 flex-wrap mb-8">
                <button
                    v-for="tab in tabs"
                    :key="tab"
                    @click="handleChange(tab)"
                    :class="`px-5 py-2 rounded-full text-sm font-medium transition-all duration-150 border
                        ${tab === activeName
                            ? 'bg-green-600 text-white border-green-600 shadow-sm'
                            : 'bg-white text-gray-600 border-gray-200 hover:border-green-400 hover:text-green-600'
                        }`"
                >
                    {{ tab }}
                </button>

                <!-- Breed breadcrumb -->
                <div v-if="pagination.breed" class="flex items-center gap-2 ml-2 text-sm text-gray-500">
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-green-600 font-medium">{{ pagination.breed }}</span>
                    <button
                        @click="clearBreed"
                        class="size-5 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors"
                    >
                        <i class="fas fa-times text-[10px] text-gray-600"></i>
                    </button>
                </div>
            </div>

            <!-- Products grid -->
            <InfScroll
                v-if="products.value.length > 0"
                :loading="loading"
                :isDisabled="isDisabled"
                :on-scroll="onLimit"
            >
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                    <div
                        v-for="product in products.value"
                        :key="product.id"
                        class="bg-white rounded-2xl shadow-sm hover:shadow-md overflow-hidden cursor-pointer group transition-all duration-200 border border-gray-100"
                        @click="() => onClickProduct(product)"
                    >
                        <!-- Image -->
                        <div class="overflow-hidden aspect-[4/3] relative">
                            <img
                                loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                :src="`${$page.props.base_img_path}${getImage(product).split('|')[0]}`"
                                alt=""
                            >
                            <!-- Category badge -->
                            <div class="absolute top-2 left-2">
                                <span class="bg-green-600/90 text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">
                                    {{ pagination.breed ? 'Rabbit' : product.category }}
                                </span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="p-3">
                            <div class="font-semibold text-gray-800 text-sm truncate">
                                {{ pagination.breed ? product.rabbit?.name : product.name }}
                            </div>
                            <div v-if="pagination.breed || product.category !== 'Rabbit'" class="text-green-600 font-bold text-sm mt-1">
                                ₱{{ Number(product.price).toLocaleString() }}.00
                            </div>
                            <div v-else class="text-gray-400 text-xs mt-1 flex items-center gap-1">
                                <i class="fas fa-paw text-[10px]"></i> View breeds
                            </div>
                        </div>
                    </div>
                </div>
            </InfScroll>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-24 text-gray-400">
                <i class="fas fa-box-open text-5xl mb-4 opacity-30"></i>
                <p class="text-base font-medium">No {{ activeName === 'All' ? 'products' : activeName.toLowerCase() }} found.</p>
                <p class="text-sm mt-1">Check back later or browse another category.</p>
            </div>

        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, reactive, onBeforeMount } from 'vue';
import InfScroll from '@/Components/InfScroll.vue';
import ImageBlg from '@/Components/ImageBlg.vue';

const { category, breed } = defineProps(['category', 'breed']);

const activeName = ref(category ?? 'All');
const products = reactive({ value: [] });
const pagination = reactive({
    category: category ?? null,
    breed: breed,
    limit: 10,
});

const loading = ref(false);
const isDisabled = ref(false);
const tabs = ['All', 'Rabbit', 'Food', 'Accesories', 'Bundle Package'];

onBeforeMount(() => fetchData());

function getImage(product) {
    if (product.rabbit) return product.rabbit.images;
    return product.image_names ?? '';
}

async function onLimit(limit) {
    pagination.limit = limit;
    return await fetchData();
}

async function fetchData() {
    loading.value = true;
    let d = false;

    await axios.get(route('product.get-user-scroll', pagination))
        .then(res => {
            if (products.value.length >= res.data.length) d = true;
            products.value = res.data;
        })
        .finally(() => { loading.value = false; });

    return d;
}

function handleChange(tab) {
    activeName.value = tab;
    pagination.breed = null;
    pagination.category = tab === 'All' ? null : tab;
    fetchData();
}

function clearBreed() {
    pagination.breed = null;
    pagination.category = activeName.value === 'All' ? null : activeName.value;
    fetchData();
}

function onClickProduct(prod) {
    if (prod.category !== 'Rabbit' || pagination.breed)
        router.get(route('product.show', prod.id));
    else {
        pagination.breed = prod.name;
        fetchData();
    }
}
</script>
