<template>
    <GuestLayout>
        <div class="min-h-screen bg-gray-50 py-10 px-4">
            <div class="max-w-3xl mx-auto">

                <!-- Back link -->
                <Link :href="route('product.show', product.id)" class="flex items-center gap-1 text-sm text-gray-500 hover:text-green-600 transition-colors mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to product
                </Link>

                <!-- Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ product.name }}</h1>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <!-- Big average score -->
                        <div class="flex flex-col items-center justify-center bg-green-50 border border-green-100 rounded-xl px-8 py-4 min-w-[120px]">
                            <span class="text-5xl font-extrabold text-green-600 leading-none">
                                {{ isNaN(rating) ? '0' : rating.toFixed(1) }}
                            </span>
                            <el-rate
                                :model-value="isNaN(rating) ? 0 : rating"
                                disabled
                                text-color="#ff9900"
                                class="mt-1"
                            />
                            <span class="text-xs text-gray-400 mt-1">out of 5</span>
                        </div>

                        <!-- Rating breakdown bars -->
                        <div class="flex-1 space-y-1.5">
                            <div v-for="star in [5,4,3,2,1]" :key="star" class="flex items-center gap-2 text-sm">
                                <span class="w-3 text-gray-500 text-right">{{ star }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div 
                                        class="h-2 rounded-full bg-yellow-400 transition-all duration-500"
                                        :style="{ width: getBarWidth(star) + '%' }"
                                    ></div>
                                </div>
                                <span class="w-6 text-gray-400 text-xs">{{ getStarCount(star) }}</span>
                            </div>
                        </div>

                        <!-- Total count -->
                        <div class="text-center sm:text-right">
                            <div class="text-3xl font-bold text-gray-800">{{ ratings.length }}</div>
                            <div class="text-sm text-gray-400">{{ ratings.length === 1 ? 'Review' : 'Reviews' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div v-if="_ratings.length === 0" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="font-medium">No reviews yet</p>
                    <p class="text-sm mt-1">Be the first to leave a review.</p>
                </div>

                <div v-else class="space-y-4">
                    <div 
                        v-for="rate in _ratings" 
                        :key="rate.id"
                        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 transition-shadow hover:shadow-md"
                    >
                        <!-- Reviewer header -->
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-3">
                                <!-- Avatar -->
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 font-bold flex items-center justify-center text-sm flex-shrink-0 uppercase">
                                    {{ rate.user.name.charAt(0) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm">{{ rate.user.name }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ formatDate(rate.created_at) }}
                                    </div>
                                </div>
                            </div>
                            <!-- Stars -->
                            <div class="flex items-center gap-1">
                                <template v-for="i in 5" :key="i">
                                    <svg 
                                        :class="i <= parseInt(rate.rate) ? 'text-yellow-400' : 'text-gray-200'"
                                        xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </template>
                                <span class="text-xs text-gray-400 ml-1">{{ rate.rate }}/5</span>
                            </div>
                        </div>

                        <!-- Comment -->
                        <p v-if="rate.comment" class="mt-4 text-gray-700 text-sm leading-relaxed">
                            {{ rate.comment }}
                        </p>

                        <!-- Media attachments -->
                        <div v-if="rate.images && rate.images.length" class="mt-4">
                            <div class="border-t border-gray-100 pt-4 flex gap-2 flex-wrap">
                                <template v-for="img in rate.images" :key="img.name">
                                    <!-- Video -->
                                    <div
                                        v-if="img.is_video"
                                        @click="previewSrc = $page.props.base_img_path + 'reviews/' + img.name"
                                        class="relative w-24 h-20 rounded-xl border border-gray-200 overflow-hidden cursor-pointer hover:scale-105 transition-transform shadow-sm"
                                    >
                                        <video autoplay="false" class="size-full object-cover">
                                            <source :src="$page.props.base_img_path + 'reviews/' + img.name" type="video/mp4">
                                        </video>
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                            <i class="fas fa-circle-play text-3xl text-white drop-shadow"></i>
                                        </div>
                                    </div>
                                    <!-- Image -->
                                    <img 
                                        v-else 
                                        loading="lazy" 
                                        @click="previewSrc = $page.props.base_img_path + 'reviews/' + img.name"
                                        class="w-24 h-20 rounded-xl object-cover border border-gray-200 cursor-pointer hover:scale-105 transition-transform shadow-sm" 
                                        :src="$page.props.base_img_path + 'reviews/' + img.name" 
                                        alt="Review image"
                                    >
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </GuestLayout>

    <ImagePreview v-if="previewSrc" :src="previewSrc" :on-close="() => previewSrc = null"/>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import ImagePreview from '@/Components/ImagePreview.vue';

const { product, ratings } = defineProps(['product', 'ratings']);

const rating = computed(() =>
    ratings.reduce((total, val) => total + parseInt(val.rate), 0) / ratings.length
);

const _ratings = computed(() =>
    ratings.map(val => {
        let imgs = [];
        if (val.images) {
            val.images.split('|').forEach(name => {
                imgs.push({ name, is_video: isVideo(name) });
            });
        }
        return { ...val, images: imgs };
    })
);

const previewSrc = ref(null);

function getStarCount(star) {
    return ratings.filter(r => parseInt(r.rate) === star).length;
}

function getBarWidth(star) {
    if (!ratings.length) return 0;
    return (getStarCount(star) / ratings.length) * 100;
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function isVideo(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    return ['mp4', 'mov', 'avi'].includes(ext);
}

onMounted(() => {
    document.querySelectorAll('.preview-video').forEach(v => v.pause());
});
</script>
