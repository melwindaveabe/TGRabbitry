<template>
    <GuestLayout>

        <!-- Hero Carousel -->
        <div class="relative">
            <el-carousel
                :interval="4000"
                height="85vh"
                arrow="always"
                indicator-position="outside"
            >
                <el-carousel-item v-for="item in ['c1.jpg', 'c2.jpg', 'c3.jpg']" :key="item">
                    <div class="relative w-full h-full">
                        <img
                            loading="lazy"
                            :src="'images/website/' + item"
                            class="w-full h-full object-cover"
                            alt=""
                        >
                        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/20 to-transparent"></div>
                    </div>
                </el-carousel-item>
            </el-carousel>

            <!-- Hero text overlay -->
            <div class="absolute inset-0 z-10 flex flex-col justify-center px-8 md:px-20 pointer-events-none">
                <p class="text-green-400 font-semibold text-sm uppercase tracking-widest mb-2">Welcome to</p>
                <h1 class="text-white text-4xl md:text-6xl font-extrabold leading-tight max-w-xl drop-shadow-lg">
                    {{ $page.props.setting?.name ?? 'TGRabbitry' }}
                </h1>
                <p class="text-white/80 mt-4 text-base md:text-lg max-w-md leading-relaxed">
                    Quality rabbits, fresh produce, and expert care — all in one place.
                </p>
                <div class="flex gap-3 mt-8 pointer-events-auto">
                    <button
                        @click="onShop('Rabbit')"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-lg"
                    >
                        Shop Now
                    </button>
                    <Link
                        :href="route('about')"
                        class="px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold rounded-xl text-sm transition-colors border border-white/30"
                    >
                        Learn More
                    </Link>
                </div>
            </div>
        </div>

        <!-- Feature icons -->
        <div class="max-w-5xl mx-auto px-6 py-14">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <HomeIcon
                    bg="bg-purple-500"
                    title="Free Shipping"
                    icon="fas fa-truck"
                    desc="On orders over ₱500"
                />
                <HomeIcon
                    bg="bg-green-500"
                    title="Superior Quality"
                    icon="fas fa-award"
                    desc="Quality products"
                />
                <HomeIcon
                    bg="bg-orange-400"
                    title="24/7 Support"
                    icon="fas fa-headset"
                    desc="Always here to help"
                />
            </div>
        </div>

        <!-- Category section -->
        <div class="max-w-7xl mx-auto px-6 pb-10">
            <div class="text-center mb-10">
                <p class="text-green-600 italic text-sm font-medium">Browse by Category</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-1">Our Products</h2>
                <p class="text-gray-500 mt-2 max-w-xl mx-auto text-sm leading-relaxed">
                    Did you know rabbits "binky" when they're really happy? They jump and twist in the air!
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                    v-for="cat in [
                        { label: 'Rabbit', img: 'images/website/r1.jpg', key: 'Rabbit' },
                        { label: 'Food', img: 'images/website/food.png', key: 'Food' },
                        { label: 'Accessories', img: 'images/website/acc.jpg', key: 'Accesories' },
                        { label: 'Bundle Package', img: 'images/website/r2.jpg', key: 'Bundle Package' },
                    ]"
                    :key="cat.key"
                    class="relative overflow-hidden rounded-2xl cursor-pointer group shadow-md"
                    @click="onShop(cat.key)"
                >
                    <img
                        loading="lazy"
                        :src="cat.img"
                        class="w-full h-[220px] object-cover transition-transform duration-500 group-hover:scale-110"
                        alt=""
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <div class="text-white font-bold text-lg">{{ cat.label }}</div>
                        <div class="mt-1 inline-flex items-center gap-1 text-green-400 text-sm font-medium group-hover:gap-2 transition-all">
                            Shop Now <i class="fas fa-arrow-right text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products grid -->
        <div class="max-w-7xl mx-auto px-6 pb-16">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">All Products</h2>
                <div class="w-12 h-1 bg-green-500 rounded mx-auto mt-2"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="bg-white rounded-2xl shadow-sm hover:shadow-md overflow-hidden cursor-pointer group transition-shadow duration-200 border border-gray-100"
                    @click="() => onClickProduct(product)"
                >
                    <div class="overflow-hidden aspect-[4/3]">
                        <img
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                            :src="'images/' + (product.image_names ?? '').split('|')[0]"
                            alt=""
                            loading="lazy"
                        >
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-gray-800 text-sm truncate">{{ product.name }}</div>
                        <div v-if="product.category !== 'Rabbit'" class="text-green-600 font-bold text-sm mt-1">
                            ₱{{ product.price.toLocaleString() }}.00
                        </div>
                        <div v-else class="text-gray-400 text-xs mt-1">View breeds</div>
                    </div>
                </div>
            </div>
        </div>

    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import HomeIcon from '@/Components/HomeIcon.vue';
import { router, Link, usePage } from '@inertiajs/vue3';

const { products } = defineProps(['products']);

function onClickProduct(prod) {
    if (prod.category === 'Rabbit')
        router.get(route('product.per-breed', prod.name));
    else
        router.get(route('product.show', prod.id));
}

function onShop(category) {
    router.get(route('shop.per-category', category));
}
</script>

<style scoped>
:deep(.el-carousel__indicators--outside) {
    margin-top: 8px;
}
:deep(.el-carousel__button) {
    background-color: #16a34a;
    width: 24px;
    height: 4px;
    border-radius: 2px;
}
</style>
