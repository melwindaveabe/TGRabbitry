<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="flex h-screen overflow-hidden">

        <!-- Left: Hero image panel -->
        <div
            class="hidden min-[800px]:flex w-[55%] relative overflow-hidden"
            :style="`background-image: url(${$page.props.base_img_path}website/m.jpg); background-size: cover; background-position: center;`"
        >
            <!-- Dark overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-black/60 via-black/30 to-transparent"></div>

            <!-- Branding text on image -->
            <div class="relative z-10 flex flex-col justify-end p-12 pb-16 text-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-full bg-green-500 flex items-center justify-center">
                        <i class="fas fa-paw text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold tracking-wide">{{ $page.props.setting?.name ?? 'TGRabbitry' }}</span>
                </div>
                <h1 class="text-4xl font-extrabold leading-tight mb-3">
                    Your trusted<br>rabbit farm.
                </h1>
                <p class="text-white/70 text-sm max-w-xs leading-relaxed">
                    Quality rabbits, fresh produce, and expert care — all in one place.
                </p>
            </div>
        </div>

        <!-- Right: Login form panel -->
        <div class="flex-1 flex flex-col justify-center items-center bg-white px-6 py-10 relative">

            <!-- Mobile logo -->
            <div class="min-[800px]:hidden flex items-center gap-2 mb-8">
                <div class="size-9 rounded-full bg-green-500 flex items-center justify-center">
                    <i class="fas fa-paw text-white"></i>
                </div>
                <span class="text-lg font-bold text-gray-800">{{ $page.props.setting?.name ?? 'TGRabbitry' }}</span>
            </div>

            <div class="w-full max-w-[400px]">

                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
                    <p class="text-gray-500 text-sm mt-1">Sign in to your account to continue</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
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
                        <p v-if="form.errors?.email" class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ form.errors.email }}
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
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full pl-10 pr-11 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent focus:bg-white transition-all"
                                :class="{ 'border-red-400 bg-red-50': form.errors?.password }"
                            >
                            <button
                                type="button"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                @click="showPassword = !showPassword"
                            >
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                        <p v-if="form.errors?.password" class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation"></i> {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center gap-2">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="size-4 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer"
                        >
                        <label for="remember" class="text-sm text-gray-600 cursor-pointer select-none">Remember me</label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold text-sm transition-colors disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <span>{{ form.processing ? 'Signing in...' : 'Sign in' }}</span>
                    </button>

                </form>

                <!-- Divider -->
                <div class="flex items-center gap-3 my-6">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-xs text-gray-400">or</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <!-- Sign up link -->
                <Link :href="route('register')">
                    <button
                        type="button"
                        class="w-full py-3 rounded-xl border-2 border-green-600 text-green-600 hover:bg-green-50 font-semibold text-sm transition-colors"
                    >
                        Create an account
                    </button>
                </Link>

                <p class="text-center text-xs text-gray-400 mt-6">
                    By signing in, you agree to our
                    <span class="text-green-600 cursor-pointer hover:underline">Terms</span> and
                    <span class="text-green-600 cursor-pointer hover:underline">Privacy Policy</span>.
                </p>
            </div>
        </div>
    </div>
</template>
