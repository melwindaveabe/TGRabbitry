<script setup>
import { ref, reactive, onBeforeMount, onBeforeUnmount } from 'vue';
import NavItem from '@/Components/NavItem.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { refreshCartCount, cartCount } from '../Methods/CartCount.js';

const deal_date_until = usePage().props.setting.deal_date_until;

const date = reactive({ days: 0, hours: 0, minutes: 0, seconds: 0 });
const showMobileNav = ref(false);
const scrolled = ref(false);
const windowWidth = ref(window.innerWidth);

window.addEventListener('resize', () => {
    windowWidth.value = window.innerWidth;
    if (window.innerWidth > 960) showMobileNav.value = false;
});

window.addEventListener('scroll', () => {
    scrolled.value = window.scrollY > 20;
});

const targetDate = new Date(deal_date_until);
let interval = null;

onBeforeMount(() => {
    refreshCartCount();
    if (deal_date_until)
        interval = setInterval(calculateTimeDifference, 1000);
});

onBeforeUnmount(() => {
    if (interval) clearInterval(interval);
});

function calculateTimeDifference() {
    const difference = targetDate - new Date();
    date.days    = Math.floor(difference / (1000 * 60 * 60 * 24));
    date.hours   = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    date.minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
    date.seconds = Math.floor((difference % (1000 * 60)) / 1000);
}
</script>

<template>
    <!-- Navbar -->
    <nav
        :class="`fixed top-0 left-0 w-full h-[var(--nav-height)] z-[999] transition-all duration-300
            ${scrolled ? 'bg-white shadow-md' : 'bg-green-600'}
        `"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-full flex items-center justify-between">

            <!-- Logo -->
            <Link :href="route('home')" class="flex-shrink-0 flex items-center gap-2">
                <img
                    :src="`${$page.props.base_img_path}website/${$page.props.setting.logo}`"
                    class="h-[36px] w-auto object-contain"
                    alt="Logo"
                    @error="e => e.target.style.display='none'"
                >
                <span
                    :class="`font-bold text-base tracking-wide ${scrolled ? 'text-green-700' : 'text-white'}`"
                >{{ $page.props.setting.name }}</span>
            </Link>

            <!-- Desktop nav -->
            <div v-if="windowWidth > 960" class="flex items-center h-full gap-1">
                <NavItem :href="route('home')" label="Home" :scrolled="scrolled" />
                <NavItem :href="route('shop')" label="Shop" :scrolled="scrolled" />
                <NavItem :href="route('blog')" label="Blog" :scrolled="scrolled" />
                <NavItem :href="route('about')" label="About" :scrolled="scrolled" />
                <NavItem v-if="$page.props.user" :href="route('message')" label="Messages" :scrolled="scrolled" />
                <NavItem :href="route('profile')" label="Profile" :scrolled="scrolled" />

                <!-- Cart button -->
                <Link
                    :href="route('cart.index')"
                    :class="`relative ml-2 flex items-center justify-center size-9 rounded-full transition-colors
                        ${scrolled ? 'bg-green-100 hover:bg-green-200 text-green-700' : 'bg-white/20 hover:bg-white/30 text-white'}`"
                >
                    <i class="fas fa-cart-shopping text-sm"></i>
                    <span
                        v-if="cartCount"
                        class="absolute -top-1 -right-1 size-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"
                    >{{ cartCount }}</span>
                </Link>
            </div>

            <!-- Mobile hamburger -->
            <button
                v-else
                :class="`p-2 rounded-lg transition-colors ${scrolled ? 'text-gray-700 hover:bg-gray-100' : 'text-white hover:bg-white/20'}`"
                @click="showMobileNav = !showMobileNav"
            >
                <i :class="showMobileNav ? 'fas fa-times' : 'fas fa-bars'" class="text-lg"></i>
            </button>
        </div>

        <!-- Mobile dropdown -->
        <div
            v-if="windowWidth <= 960 && showMobileNav"
            class="absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100 py-2 z-50"
        >
            <Link v-for="item in [
                { label: 'Home', route: route('home') },
                { label: 'Shop', route: route('shop') },
                { label: 'Cart', route: route('cart.index') },
                { label: 'Blog', route: route('blog') },
                { label: 'About', route: route('about') },
                { label: 'Profile', route: route('profile') },
            ]" :key="item.label" :href="item.route"
                class="flex items-center px-5 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 font-medium text-sm transition-colors"
                @click="showMobileNav = false"
            >
                {{ item.label }}
            </Link>
            <Link v-if="$page.props.user" :href="route('message')"
                class="flex items-center px-5 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 font-medium text-sm transition-colors"
                @click="showMobileNav = false"
            >Messages</Link>
        </div>
    </nav>

    <!-- Page content -->
    <div class="bg-gray-50 mt-[var(--nav-height)]">
        <slot />

        <!-- Deal / Countdown section -->
        <div
            style="background-image: url('/images/website/deals.jpg');"
            class="relative flex items-center w-full bg-cover bg-center min-h-[480px] mt-20"
        >
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/30"></div>

            <div class="relative z-10 w-full max-w-7xl mx-auto px-6 flex justify-end">
                <div class="w-full md:w-[50%] lg:w-[42%]">
                    <!-- Deal card -->
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-8 text-white shadow-2xl">
                        <div class="inline-flex items-center gap-2 bg-green-500/30 border border-green-400/40 rounded-full px-3 py-1 mb-4">
                            <i class="fas fa-tag text-green-300 text-xs"></i>
                            <span class="text-green-300 text-xs font-semibold uppercase tracking-wider">Limited Offer</span>
                        </div>

                        <div v-html="$page.props.setting.deal_desc" class="text-white [&_h1]:text-3xl [&_h1]:font-bold [&_h2]:text-2xl [&_h2]:font-bold [&_p]:text-white/80 [&_p]:text-sm [&_p]:mt-1 [&_strong]:text-white"></div>

                        <!-- Countdown -->
                        <div class="grid grid-cols-4 gap-3 mt-7">
                            <div
                                v-for="(val, label) in { DAYS: date.days, HOURS: date.hours, MINS: date.minutes, SECS: date.seconds }"
                                :key="label"
                                class="bg-white/15 border border-white/20 rounded-2xl py-4 text-center"
                            >
                                <div class="text-3xl font-extrabold text-white tabular-nums">
                                    {{ String(val).padStart(2, '0') }}
                                </div>
                                <div class="text-[10px] font-semibold tracking-widest mt-1.5 text-white/60 uppercase">{{ label }}</div>
                            </div>
                        </div>

                        <Link :href="route('shop')" class="mt-6 inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors shadow-lg">
                            Shop the Deal <i class="fas fa-arrow-right text-xs"></i>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-950 text-gray-400">
            <!-- Top footer -->
            <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-1 md:grid-cols-3 gap-12">

                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="size-8 rounded-lg bg-green-600 flex items-center justify-center">
                            <i class="fas fa-paw text-white text-sm"></i>
                        </div>
                        <span class="text-white font-bold text-lg">{{ $page.props.setting.name }}</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        The goal of {{ $page.props.setting.name }} is to share our lineage with people who also want to start a rabbit farm — either in the yard or on a large scale.
                    </p>
                    <div class="flex gap-3 mt-6">
                        <a href="https://www.facebook.com/HomeOfQualityRabbitBreed" target="_blank"
                            class="size-9 rounded-xl bg-white/5 hover:bg-green-600 border border-white/10 flex items-center justify-center transition-all duration-200">
                            <i class="fa-brands fa-facebook text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick links -->
                <div>
                    <div class="text-white font-semibold mb-5 text-sm uppercase tracking-widest">Quick Links</div>
                    <ul class="space-y-3">
                        <li v-for="item in [
                            { label: 'Home', href: route('home'), icon: 'fas fa-house' },
                            { label: 'Shop', href: route('shop'), icon: 'fas fa-cart-shopping' },
                            { label: 'Blog', href: route('blog'), icon: 'fas fa-pen-to-square' },
                            { label: 'About', href: route('about'), icon: 'fas fa-circle-info' },
                        ]" :key="item.label">
                            <Link :href="item.href" class="flex items-center gap-2.5 text-sm text-gray-500 hover:text-green-400 transition-colors group">
                                <i :class="`${item.icon} text-xs text-gray-600 group-hover:text-green-500 transition-colors`"></i>
                                {{ item.label }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <div class="text-white font-semibold mb-5 text-sm uppercase tracking-widest">Contact Us</div>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="size-8 rounded-lg bg-green-600/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-location-dot text-green-400 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-500 leading-relaxed">Roxas, San Mariano, Orkidyas Street</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="size-8 rounded-lg bg-green-600/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-green-400 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-500">0995 885 3635</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="size-8 rounded-lg bg-green-600/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-brands fa-facebook text-green-400 text-xs"></i>
                            </div>
                            <a href="https://www.facebook.com/HomeOfQualityRabbitBreed" target="_blank"
                                class="text-sm text-gray-500 hover:text-green-400 transition-colors">
                                {{ $page.props.setting.name }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="border-t border-white/5 py-5">
                <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <span class="text-xs text-gray-600">
                        © {{ new Date().getFullYear() }} {{ $page.props.setting.name }}. All rights reserved.
                    </span>
                    <div class="flex items-center gap-1 text-xs text-gray-600">
                        <i class="fas fa-paw text-green-700 text-[10px]"></i>
                        Made with care for rabbit lovers
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
