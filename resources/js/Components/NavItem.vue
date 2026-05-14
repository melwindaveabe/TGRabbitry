<template>
    <Link
        :href="href"
        :class="`relative h-full flex items-center px-3 text-sm font-medium transition-colors duration-150 cursor-pointer
            ${active
                ? (scrolled
                    ? 'text-green-700 after:absolute after:bottom-0 after:left-0 after:w-full after:h-[3px] after:bg-green-600 after:rounded-t'
                    : 'text-white after:absolute after:bottom-0 after:left-0 after:w-full after:h-[3px] after:bg-white after:rounded-t')
                : (scrolled
                    ? 'text-gray-600 hover:text-green-700'
                    : 'text-white/80 hover:text-white')
            }`"
    >
        <i v-if="icon" :class="`${icon} mr-1.5 text-xs`"></i>
        {{ label }}
        <slot />
    </Link>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { href, label, icon, scrolled } = defineProps(['href', 'label', 'icon', 'scrolled']);
const page = usePage();
const active = computed(() => {
    const path = new URL(href, window.location.origin).pathname;
    return page.url === path || page.url.startsWith(path + '/');
});
</script>
