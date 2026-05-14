<template>
    <Link :href="href" @click="closeIfMobile">
        <div
            :class="`w-full flex items-center gap-3 px-4 py-2.5 cursor-pointer font-medium text-sm transition-all duration-150 rounded-lg mx-1 my-0.5
                ${isActive
                    ? 'bg-green-600 text-white shadow-sm'
                    : 'text-gray-600 hover:bg-green-50 hover:text-green-700'
                }`"
            :style="isActive ? 'width: calc(100% - 8px)' : 'width: calc(100% - 8px)'"
        >
            <i v-if="icon" :class="`${icon} w-4 text-center text-[14px]`"></i>
            <span>{{ label }}</span>
        </div>
    </Link>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { href, icon, label, closeIfMobile } = defineProps(['href', 'icon', 'label', 'closeIfMobile']);

const page = usePage();
const isActive = computed(() => page.url.startsWith(new URL(href, window.location.origin).pathname));
</script>
