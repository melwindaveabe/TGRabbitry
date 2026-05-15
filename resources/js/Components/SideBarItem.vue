<template>
    <Link :href="href" @click="closeIfMobile">
        <div
            :class="`w-full flex items-center gap-3 px-4 py-2.5 cursor-pointer font-medium text-sm transition-all duration-150 rounded-lg mx-1 my-0.5
                ${isActive
                    ? 'bg-green-600 text-white shadow-sm'
                    : 'text-gray-600 hover:bg-green-50 hover:text-green-700'
                }`"
            style="width: calc(100% - 8px)"
        >
            <i v-if="icon" :class="`${icon} w-4 text-center text-[14px]`"></i>
            <span>{{ label }}</span>
        </div>
    </Link>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { href, icon, label, closeIfMobile, exact, inactive } = defineProps({
    href:          { type: String,   required: true },
    icon:          { type: String,   default: null },
    label:         { type: String,   required: true },
    closeIfMobile: { type: Function, default: () => {} },
    exact:         { type: Boolean,  default: false },
    // inactive=true forces the item to never highlight (e.g. when a sibling modal is open)
    inactive:      { type: Boolean,  default: false },
});

const page = usePage();

const isActive = computed(() => {
    if (inactive) return false;
    const path    = new URL(href, window.location.origin).pathname;
    const current = page.url.split('?')[0];
    if (exact) return current === path;
    return current === path || current.startsWith(path + '/');
});
</script>
