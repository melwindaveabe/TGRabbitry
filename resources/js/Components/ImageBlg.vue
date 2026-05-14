<template>
    <div
        class="relative bg-center bg-cover h-[320px] md:h-[400px] flex items-center justify-center overflow-hidden"
        :style="`background-image: url('${$page.props.base_img_path}website/${image}');`"
    >
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-black/60"></div>

        <!-- Breadcrumb + Title -->
        <div class="relative z-10 text-center px-4">
            <div class="flex items-center justify-center gap-2 text-white/70 text-sm mb-3">
                <Link :href="route('home')" class="hover:text-white transition-colors">Home</Link>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-white">{{ title }}</span>
            </div>
            <h1 class="text-white text-4xl md:text-5xl font-extrabold tracking-tight drop-shadow-lg">
                {{ title }}
            </h1>
            <div v-if="desc" class="text-white/80 mt-3 text-base max-w-xl mx-auto">{{ desc }}</div>
            <div class="w-12 h-1 bg-green-400 rounded mx-auto mt-4"></div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
const { image, title, desc } = defineProps(['image', 'title', 'desc']);
</script>
