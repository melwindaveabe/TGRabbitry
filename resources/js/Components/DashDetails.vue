<template>
    <div :class="`${className ?? 'w-[300px]'} bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4 hover:shadow-md transition-shadow duration-200`">
        <div :class="`flex-shrink-0 size-12 rounded-xl flex items-center justify-center text-white text-lg`"
            :style="`background-color: ${bgColor}`">
            <i :class="icon"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">{{ title }}</div>
            <div class="text-xl font-bold text-gray-800 mt-0.5">{{ value }}</div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const { icon, title, value, iconColor, className } = defineProps(['icon', 'title', 'value', 'iconColor', 'className']);

const bgColor = computed(() => {
    const map = {
        red: '#ef4444',
        green: '#22c55e',
        blue: '#3b82f6',
        yellow: '#f59e0b',
        purple: '#8b5cf6',
        cyan: '#06b6d4',
    };
    return map[iconColor] ?? '#22c55e';
});
</script>
