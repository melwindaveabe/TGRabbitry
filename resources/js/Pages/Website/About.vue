<template>
    <GuestLayout>
        <ImageBlg title="About" image="Baby3.jpg" />

        <!-- Intro section -->
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- Left: Brand card -->
                <div class="flex flex-col items-center text-center bg-white rounded-3xl shadow-sm border border-gray-100 p-10">
                    <div class="size-20 rounded-2xl bg-green-50 flex items-center justify-center mb-5 shadow-sm">
                        <img
                            :src="`/images/website/${$page.props.setting.logo}`"
                            class="h-12 w-auto object-contain"
                            alt="Logo"
                            @error="e => e.target.style.display='none'"
                        >
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ $page.props.setting.name }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        We also post regular updates about our business on our Facebook Page
                        <span class="text-green-600 font-medium">@{{ $page.props.setting.name }}</span> 😊
                        Feel free to send us a message if you have any questions regarding rabbit care.
                    </p>

                    <!-- Stats row -->
                    <div class="grid grid-cols-3 gap-4 w-full mt-2">
                        <div class="bg-green-50 rounded-2xl py-4 text-center">
                            <div class="text-2xl font-extrabold text-green-700">2021</div>
                            <div class="text-xs text-gray-500 mt-1">Founded</div>
                        </div>
                        <div class="bg-green-50 rounded-2xl py-4 text-center">
                            <div class="text-2xl font-extrabold text-green-700">6+</div>
                            <div class="text-xs text-gray-500 mt-1">Rabbit Breeds</div>
                        </div>
                        <div class="bg-green-50 rounded-2xl py-4 text-center">
                            <div class="text-2xl font-extrabold text-green-700">100%</div>
                            <div class="text-xs text-gray-500 mt-1">Quality</div>
                        </div>
                    </div>

                    <!-- Social link -->
                    <a
                        href="https://www.facebook.com/HomeOfQualityRabbitBreed"
                        target="_blank"
                        class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors"
                    >
                        <i class="fa-brands fa-facebook"></i> Follow on Facebook
                    </a>
                </div>

                <!-- Right: Story text -->
                <div>
                    <p class="text-green-600 font-semibold text-sm uppercase tracking-widest mb-2">Our Story</p>
                    <h2 class="text-4xl font-extrabold text-gray-900 leading-tight mb-2">
                        Welcome to<br>
                        <span class="text-green-600">{{ $page.props.setting.name }}</span>
                    </h2>
                    <div class="w-12 h-1 bg-green-500 rounded mb-6"></div>

                    <div class="space-y-4 text-gray-600 text-sm leading-relaxed">
                        <p>
                            {{ $page.props.setting.name }} is a rabbit owner's ultimate destination for all things bunny-related in Roxas, Philippines.
                            Our online store offers a wide range of essential products tailored to meet the needs of rabbit owners,
                            including high-quality rabbit pellets and more.
                        </p>
                        <p>
                            {{ $page.props.setting.name }} was founded by Mr. Salas, a passionate rabbit owner whose love for these furry friends
                            began in 2021. Starting from a personal journey with six beloved rabbits, the struggle to find the right products
                            locally led to the realization of a larger issue faced by many rabbit owners in the Philippines.
                        </p>
                        <p>
                            With limited availability of essential items, owners often had to rely on overseas purchases.
                            Determined to change this, {{ $page.props.setting.name }} was launched to provide a convenient and reliable
                            source of rabbit care products right here in Roxas.
                        </p>
                        <p>
                            Beyond being a store, {{ $page.props.setting.name }} is a community hub where rabbit enthusiasts can connect,
                            share experiences, and gain valuable knowledge to enhance the well-being of rabbits across the country.
                        </p>
                    </div>

                    <div class="flex gap-3 mt-8">
                        <Link :href="route('shop')"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                            Browse Shop
                        </Link>
                        <Link :href="route('message')"
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition-colors">
                            Contact Us
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values section -->
        <div class="bg-white border-t border-gray-100 py-16">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-12">
                    <p class="text-green-600 font-semibold text-sm uppercase tracking-widest mb-2">Why Choose Us</p>
                    <h2 class="text-3xl font-bold text-gray-900">Our Values</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="val in values" :key="val.title"
                        class="flex flex-col items-center text-center p-6 rounded-2xl bg-gray-50 hover:bg-green-50 transition-colors group">
                        <div class="size-14 rounded-2xl bg-green-100 group-hover:bg-green-200 flex items-center justify-center mb-4 transition-colors">
                            <i :class="`${val.icon} text-green-600 text-xl`"></i>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">{{ val.title }}</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">{{ val.desc }}</p>
                    </div>
                </div>
            </div>
        </div>

    </GuestLayout>
</template>

<script setup>
import ImageBlg from '@/Components/ImageBlg.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Link } from '@inertiajs/vue3';

const values = [
    {
        icon: 'fas fa-award',
        title: 'Quality First',
        desc: 'Every product we offer is carefully selected to ensure the best for your rabbits.',
    },
    {
        icon: 'fas fa-heart',
        title: 'Passion Driven',
        desc: 'Founded by rabbit lovers, we understand what your furry friends truly need.',
    },
    {
        icon: 'fas fa-truck',
        title: 'Fast Delivery',
        desc: 'We deliver right to your door so you never run out of essentials.',
    },
    {
        icon: 'fas fa-users',
        title: 'Community',
        desc: 'Join a growing community of rabbit enthusiasts sharing knowledge and care tips.',
    },
];
</script>
