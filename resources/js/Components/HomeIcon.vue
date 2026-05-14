<template>
    <div class="flex flex-col items-center text-center group">
        <div :class="`${bg} size-20 rounded-2xl flex items-center justify-center text-white text-3xl shadow-md group-hover:scale-105 transition-transform duration-200`">
            <i :class="icon"></i>
        </div>
        <div class="mt-3 font-semibold text-gray-800 uppercase text-sm tracking-wide">{{ title }}</div>
        <div class="text-gray-500 text-xs mt-0.5 uppercase">{{ desc }}</div>
    </div>
</template>

<script setup>
const { bg, title, desc, icon } = defineProps(['bg', 'title', 'desc', 'icon']);
</script>
