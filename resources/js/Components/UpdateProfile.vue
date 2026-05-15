<template>
    <el-dialog
        :width="isPC() ? '480px' : '92%'"
        v-model="showProfileModal"
        :show-close="true"
        :close-on-click-modal="false"
        class="update-profile-dialog"
    >
        <!-- Custom header -->
        <template #header>
            <div class="flex items-center gap-3 px-1">
                <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-pen text-green-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 leading-tight">Update Profile</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Keep your information up to date</p>
                </div>
            </div>
        </template>

        <!-- Avatar upload -->
        <div class="flex flex-col items-center mb-6">
            <div class="relative group cursor-pointer" @click="triggerFileInput">
                <img
                    loading="lazy"
                    :src="previewUrl || `${$page.props.base_img_path}${$page.props.user.image_name}`"
                    class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg ring-2 ring-green-200"
                    alt="Profile photo"
                >
                <!-- Hover overlay -->
                <div class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <i class="fas fa-camera text-white text-xl"></i>
                </div>
            </div>
            <input
                ref="fileInput"
                @change="onChangeImage"
                type="file"
                accept="image/*"
                class="hidden"
            >
            <button
                @click="triggerFileInput"
                class="mt-2 text-xs text-green-600 hover:text-green-700 font-medium flex items-center gap-1 transition-colors"
            >
                <i class="fas fa-upload text-[10px]"></i>
                Change photo
            </button>
        </div>

        <!-- Form fields -->
        <div class="space-y-4">

            <!-- Name -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Name <span class="text-red-500">*</span>
                </label>
                <el-input
                    v-model="form_data.name"
                    placeholder="Enter your name"
                    type="text"
                    clearable
                    class="w-full"
                >
                    <template #prefix>
                        <i class="fas fa-user text-gray-400 text-xs"></i>
                    </template>
                </el-input>
                <p v-if="form_errors.value.name" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation text-[10px]"></i>
                    {{ form_errors.value.name[0] }}
                </p>
            </div>

            <!-- City -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    City <span class="text-red-500">*</span>
                </label>
                <el-select v-model="form_data.city" placeholder="Select your city" class="w-full">
                    <el-option value="Mansalay" label="Mansalay" />
                    <el-option value="Roxas" label="Roxas" />
                    <el-option value="Bongabong" label="Bongabong" />
                    <el-option value="Bansud" label="Bansud" />
                </el-select>
                <p v-if="form_errors.value.city" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation text-[10px]"></i>
                    {{ form_errors.value.city[0] }}
                </p>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Address <span class="text-red-500">*</span>
                </label>
                <el-input
                    v-model="form_data.address"
                    placeholder="Enter your address"
                    type="text"
                    clearable
                >
                    <template #prefix>
                        <i class="fas fa-location-dot text-gray-400 text-xs"></i>
                    </template>
                </el-input>
                <p v-if="form_errors.value.address" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation text-[10px]"></i>
                    {{ form_errors.value.address[0] }}
                </p>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Email <span class="text-red-500">*</span>
                </label>
                <el-input
                    v-model="form_data.email"
                    placeholder="Enter your email"
                    type="text"
                    clearable
                >
                    <template #prefix>
                        <i class="fas fa-envelope text-gray-400 text-xs"></i>
                    </template>
                </el-input>
                <p v-if="form_errors.value.email" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation text-[10px]"></i>
                    {{ form_errors.value.email[0] }}
                </p>
            </div>

            <!-- Phone (non-admin only) -->
            <div v-if="$page.props.user.admin == 0">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Phone No. <span class="text-red-500">*</span>
                </label>
                <el-input
                    v-model="form_data.phone_no"
                    placeholder="Enter phone number"
                    type="text"
                    clearable
                >
                    <template #prefix>
                        <i class="fas fa-phone text-gray-400 text-xs"></i>
                    </template>
                </el-input>
                <p v-if="form_errors.value.phone_no" class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation text-[10px]"></i>
                    {{ form_errors.value.phone_no[0] }}
                </p>
            </div>

        </div>

        <!-- Footer -->
        <template #footer>
            <div class="flex justify-end gap-2 pt-2">
                <button
                    @click="showProfileModal = false"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="onSubmitUpdate"
                    :disabled="saving"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors flex items-center gap-2 shadow-sm shadow-green-200"
                >
                    <i v-if="saving" class="fas fa-spinner fa-spin text-xs"></i>
                    <i v-else class="fas fa-check text-xs"></i>
                    {{ saving ? 'Saving…' : 'Save Changes' }}
                </button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, reactive, watch } from 'vue';

const user = usePage().props.user;

const props = defineProps({
    onClose: {
        type: Function,
        default: () => {},
    },
});

const showProfileModal = ref(true);
const saving = ref(false);
const form_errors = reactive({ value: {} });
const previewUrl = ref(null);
const fileInput = ref(null);

const form_data = reactive({
    name: user.name,
    address: user.address,
    city: user.city,
    email: user.email,
    phone_no: user.phone_no,
    image: null,
});

watch(showProfileModal, (val) => {
    if (!val) props.onClose();
});

function triggerFileInput() {
    fileInput.value?.click();
}

function onChangeImage(e) {
    const file = e.target.files[0];
    if (!file) return;
    form_data.image = file;
    previewUrl.value = URL.createObjectURL(file);
}

function isPC() {
    return window.innerWidth > 960;
}

function onSubmitUpdate() {
    saving.value = true;
    form_errors.value = {};
    axios.post(route('profile.update'), form_data, {
        headers: { 'content-type': 'multipart/form-data' },
    })
    .then(() => {
        router.visit(window.location.href, { preserveScroll: true });
    })
    .catch(error => {
        form_errors.value = error.response.data.errors ?? {};
    })
    .finally(() => {
        saving.value = false;
    });
}
</script>

<style scoped>
:deep(.el-dialog__header) {
    padding: 20px 20px 12px;
    border-bottom: 1px solid #f3f4f6;
    margin-right: 0;
}
:deep(.el-dialog__body) {
    padding: 20px;
}
:deep(.el-dialog__footer) {
    padding: 12px 20px 20px;
    border-top: 1px solid #f3f4f6;
}
:deep(.el-dialog) {
    border-radius: 1rem;
}
:deep(.el-select) {
    width: 100%;
}
</style>
