<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <!-- <div class="flex h-svh bg-slate-300 items-center justify-center">
        <div class="w-[400px] text-gray-800 py-5 px-7 bg-white shadow-lg rounded-md">
            <div class="font-semibold text-[30px] mb-5 text-center">Email Verification</div>
            <div class="uppercase text-sm mb-2">Code</div>
            <input 
                v-model="code"
                type="number" 
                @keyup.enter="submit"
                class="w-full p-2 rounded-md " 
                placeholder="Type your code"
            >
            <div v-if="error" class="text-red-600 mt-1">{{error}}</div>

            <button @click="submit" class="bg-teal-800 text-white mt-4 py-2 rounded-md w-full hover:bg-teal-600">
                Verify
            </button>
            <button @click="resend" class="bg-cyan-600 text-white mt-2 py-2 rounded-md w-full hover:bg-cyan-500">
                Resend Code
            </button>
        </div>
    </div> -->

    
    <div class="flex h-svh bg-slate-300 items-center justify-center">
        <div class="w-[400px] text-gray-800 py-5 px-7 bg-white shadow-lg rounded-md">
            <div class="font-semibold text-[30px] mb-5 text-center">Email Verification</div>

        <div class="mb-4 text-sm text-gray-600">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div class="mb-4 font-medium text-sm text-green-600" v-if="verificationLinkSent">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between flex-wrap gap-5">
                <button :class="`opacity-25: ${form.processing}; bg-teal-800 text-white mt-4 py-2 rounded-md px-3 hover:bg-teal-600`" :disabled="form.processing">
                    Resend Verification Email
                </button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >Log Out</Link
                >
            </div>
        </form>
        </div>
        </div>
</template>
