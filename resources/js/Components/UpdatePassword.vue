<template>
    <el-dialog
        width="min(440px, 95%)"
        v-model="showPasswordModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lock text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Update Password</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Use a long, random password to stay secure</p>
                </div>
            </div>
        </template>

        <div class="space-y-4">

            <!-- Current Password -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <el-input
                    v-model="form_pass_data.current_password"
                    placeholder="Enter current password"
                    type="password"
                    show-password
                    clearable
                >
                    <template #prefix>
                        <i class="fas fa-lock text-gray-400 text-xs"></i>
                    </template>
                </el-input>
                <p v-if="form_errors.value?.current_password" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation text-[10px]"></i>
                    {{ form_errors.value.current_password[0] }}
                </p>
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    New Password <span class="text-red-500">*</span>
                </label>
                <el-input
                    v-model="form_pass_data.new_password"
                    placeholder="Enter new password"
                    type="password"
                    show-password
                    clearable
                >
                    <template #prefix>
                        <i class="fas fa-key text-gray-400 text-xs"></i>
                    </template>
                </el-input>
                <!-- Strength indicator -->
                <div v-if="form_pass_data.new_password" class="mt-1.5 flex items-center gap-2">
                    <div class="flex gap-1 flex-1">
                        <div v-for="i in 4" :key="i"
                            :class="['h-1 flex-1 rounded-full transition-all duration-300', i <= strength.score ? strength.color : 'bg-gray-200']"
                        ></div>
                    </div>
                    <span :class="['text-[10px] font-semibold', strength.textColor]">{{ strength.label }}</span>
                </div>
                <p v-if="form_errors.value?.new_password" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation text-[10px]"></i>
                    {{ form_errors.value.new_password[0] }}
                </p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <el-input
                    v-model="form_pass_data.confirm_password"
                    placeholder="Re-enter new password"
                    type="password"
                    show-password
                    clearable
                >
                    <template #prefix>
                        <i class="fas fa-shield-halved text-gray-400 text-xs"></i>
                    </template>
                </el-input>
                <!-- Match indicator -->
                <p v-if="form_pass_data.confirm_password && form_pass_data.new_password"
                    :class="['text-xs mt-1 flex items-center gap-1', passwordsMatch ? 'text-green-600' : 'text-red-500']">
                    <i :class="passwordsMatch ? 'fas fa-check-circle' : 'fas fa-times-circle'" class="text-[10px]"></i>
                    {{ passwordsMatch ? 'Passwords match' : 'Passwords do not match' }}
                </p>
                <p v-if="form_errors.value?.confirm_password" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation text-[10px]"></i>
                    {{ form_errors.value.confirm_password[0] }}
                </p>
            </div>

            <!-- Info note -->
            <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex items-start gap-2">
                <i class="fas fa-circle-info text-blue-400 text-xs mt-0.5 flex-shrink-0"></i>
                <p class="text-xs text-blue-600 leading-relaxed">
                    You will be logged out after updating your password. Please log in again with your new credentials.
                </p>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <button
                    @click="showPasswordModal = false"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="onSubmitUpdatePassword"
                    :disabled="saving"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors flex items-center gap-2 shadow-sm"
                >
                    <i v-if="saving" class="fas fa-spinner fa-spin text-xs"></i>
                    <i v-else class="fas fa-check text-xs"></i>
                    {{ saving ? 'Updating…' : 'Update Password' }}
                </button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, reactive, watch, computed } from 'vue';

const props = defineProps({
    onClose: { type: Function, default: () => {} },
});

const showPasswordModal = ref(true);
const saving = ref(false);
const form_errors = reactive({ value: {} });
const form_pass_data = reactive({
    current_password: null,
    new_password: null,
    confirm_password: null,
});

watch(showPasswordModal, val => { if (!val) props.onClose(); });

const passwordsMatch = computed(() =>
    form_pass_data.new_password === form_pass_data.confirm_password
);

const strength = computed(() => {
    const p = form_pass_data.new_password ?? '';
    let score = 0;
    if (p.length >= 8)  score++;
    if (/[A-Z]/.test(p)) score++;
    if (/[0-9]/.test(p)) score++;
    if (/[^A-Za-z0-9]/.test(p)) score++;

    const map = [
        { label: 'Weak',   color: 'bg-red-400',    textColor: 'text-red-500' },
        { label: 'Fair',   color: 'bg-orange-400',  textColor: 'text-orange-500' },
        { label: 'Good',   color: 'bg-yellow-400',  textColor: 'text-yellow-600' },
        { label: 'Strong', color: 'bg-green-500',   textColor: 'text-green-600' },
    ];
    return { score, ...map[Math.max(0, score - 1)] };
});

function onSubmitUpdatePassword() {
    saving.value = true;
    form_errors.value = {};
    axios.post(route('profile.update-password'), {
        current_password: form_pass_data.current_password,
        new_password: form_pass_data.new_password,
        confirm_password: form_pass_data.confirm_password,
    })
    .then(() => { router.post(route('logout')); })
    .catch(error => { form_errors.value = error.response.data.errors ?? {}; })
    .finally(() => { saving.value = false; });
}
</script>

<style scoped>
:deep(.el-dialog) { border-radius: 1rem; }
:deep(.el-dialog__header) { padding: 20px 20px 12px; border-bottom: 1px solid #f3f4f6; margin-right: 0; }
:deep(.el-dialog__body) { padding: 20px; }
:deep(.el-dialog__footer) { padding: 12px 20px 20px; border-top: 1px solid #f3f4f6; }
</style>
