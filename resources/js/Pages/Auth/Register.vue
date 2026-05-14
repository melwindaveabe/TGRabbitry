<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    address: '',
    city: '',
    phone_no: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirm = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password');
            form.reset('password_confirmation');
        },
    });
};
</script>

<template>
    <div class="flex h-screen overflow-hidden">

        <!-- Left: Form panel -->
        <div class="flex-1 flex flex-col justify-center items-center bg-white px-6 py-8 overflow-y-auto">

            <!-- Mobile logo -->
            <div class="min-[800px]:hidden flex items-center gap-2 mb-6">
                <div class="size-9 rounded-full bg-green-500 flex items-center justify-center">
                    <i class="fas fa-paw text-white"></i>
                </div>
                <span class="text-lg font-bold text-gray-800">{{ $page.props.setting?.name ?? 'TGRabbitry' }}</span>
            </div>

            <div class="w-full max-w-[440px]">

                <!-- Header -->
                <div class="mb-7">
                    <h2 class="text-3xl font-bold text-gray-900">Create account</h2>
                    <p class="text-gray-500 text-sm mt-1">Fill in your details to get started</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-user text-sm"></i>
                            </span>
                            <input
                                v-model="form.name"
                                type="text"
                                autocomplete="name"
                                placeholder="Juan dela Cruz"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent focus:bg-white transition-all"
                                :class="{ 'border-red-400 bg-red-50': form.errors?.name }"
                            >
                        </div>
                        <p v-if="form.errors?.name" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                placeholder="you@example.com"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent focus:bg-white transition-all"
                                :class="{ 'border-red-400 bg-red-50': form.errors?.email }"
                            >
                        </div>
                        <p v-if="form.errors?.email" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- City + Phone (2 columns) -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-city text-sm"></i>
                                </span>
                                <select
                                    v-model="form.city"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent focus:bg-white transition-all appearance-none"
                                    :class="{ 'border-red-400 bg-red-50': form.errors?.city }"
                                >
                                    <option value="">Select city</option>
                                    <option value="Mansalay">Mansalay</option>
                                    <option value="Roxas">Roxas</option>
                                    <option value="Bongabong">Bongabong</option>
                                    <option value="Bansud">Bansud</option>
                                </select>
                            </div>
                            <p v-if="form.errors?.city" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fas fa-circle-exclamation"></i> {{ form.errors.city }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-phone text-sm"></i>
                                </span>
                                <input
                                    v-model="form.phone_no"
                                    type="tel"
                                    placeholder="09XX XXX XXXX"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent focus:bg-white transition-all"
                                    :class="{ 'border-red-400 bg-red-50': form.errors?.phone_no }"
                                >
                            </div>
                            <p v-if="form.errors?.phone_no" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fas fa-circle-exclamation"></i> {{ form.errors.phone_no }}
                            </p>
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-location-dot text-sm"></i>
                            </span>
                            <input
                                v-model="form.address"
                                type="text"
                                placeholder="Street, Barangay"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent focus:bg-white transition-all"
                                :class="{ 'border-red-400 bg-red-50': form.errors?.address }"
                            >
                        </div>
                        <p v-if="form.errors?.address" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ form.errors.address }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                placeholder="Min. 8 characters"
                                class="w-full pl-10 pr-11 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent focus:bg-white transition-all"
                                :class="{ 'border-red-400 bg-red-50': form.errors?.password }"
                            >
                            <button type="button" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors" @click="showPassword = !showPassword">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                        <p v-if="form.errors?.password" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input
                                v-model="form.password_confirmation"
                                :type="showConfirm ? 'text' : 'password'"
                                autocomplete="new-password"
                                placeholder="Repeat your password"
                                class="w-full pl-10 pr-11 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent focus:bg-white transition-all"
                            >
                            <button type="button" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors" @click="showConfirm = !showConfirm">
                                <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold text-sm transition-colors disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 mt-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <span>{{ form.processing ? 'Creating account...' : 'Create Account' }}</span>
                    </button>

                </form>

                <!-- Divider -->
                <div class="flex items-center gap-3 my-5">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-xs text-gray-400">already have an account?</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <!-- Sign in link -->
                <Link :href="route('login')">
                    <button type="button" class="w-full py-3 rounded-xl border-2 border-green-600 text-green-600 hover:bg-green-50 font-semibold text-sm transition-colors">
                        Sign in instead
                    </button>
                </Link>

            </div>
        </div>

        <!-- Right: Hero image panel -->
        <div
            class="hidden min-[800px]:flex w-[45%] relative overflow-hidden"
            :style="`background-image: url(${$page.props.base_img_path}website/m2.jpg); background-size: cover; background-position: center;`"
        >
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-tl from-black/60 via-black/30 to-transparent"></div>

            <!-- Branding -->
            <div class="relative z-10 flex flex-col justify-end p-12 pb-16 text-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-full bg-green-500 flex items-center justify-center">
                        <i class="fas fa-paw text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold tracking-wide">{{ $page.props.setting?.name ?? 'TGRabbitry' }}</span>
                </div>
                <h1 class="text-4xl font-extrabold leading-tight mb-3">
                    Join our<br>community.
                </h1>
                <p class="text-white/70 text-sm max-w-xs leading-relaxed">
                    Register now and start browsing our selection of quality rabbits and farm products.
                </p>
            </div>
        </div>

    </div>
</template>
