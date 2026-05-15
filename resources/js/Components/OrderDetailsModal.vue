<template>
    <!-- Order details dialog -->
    <el-dialog
        width="min(520px, 95%)"
        v-model="showOrderModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-box text-green-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Order Details</h2>
                    <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                        <i class="fas fa-location-dot text-[10px]"></i>
                        {{ props.order.address }}
                    </p>
                </div>
            </div>
        </template>

        <!-- Status badges -->
        <div class="flex gap-2 flex-wrap mb-4">
            <span :class="`text-xs font-semibold px-3 py-1 rounded-full ${getStatusBadge(props.order.status)}`">
                <i :class="getStatusIcon(props.order.status)" class="mr-1 text-[10px]"></i>
                {{ props.order.status }}
            </span>
            <span :class="`text-xs font-semibold px-3 py-1 rounded-full ${props.order.paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`">
                <i :class="props.order.paid ? 'fas fa-check' : 'fas fa-clock'" class="mr-1 text-[10px]"></i>
                {{ props.order.paid ? 'Paid' : 'Unpaid' }}
            </span>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-gray-100 text-gray-600">
                <i class="fas fa-credit-card mr-1 text-[10px]"></i>
                {{ props.order.payment_method }}
            </span>
        </div>

        <!-- Items list -->
        <div class="bg-gray-50 rounded-xl border border-gray-100 divide-y divide-gray-100 overflow-hidden mb-4">
            <div
                v-for="row in props.order.details"
                :key="row.id"
                class="flex items-center gap-3 px-4 py-3"
            >
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ row.product.name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        ₱{{ Number(row.product.price).toLocaleString() }} × {{ row.qty }}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-bold text-green-600 text-sm">₱{{ getTotal(row).toLocaleString() }}</p>
                    <button
                        v-if="props.order.status === 'Delivered' && !row.rate"
                        @click="addRating(row.id)"
                        class="mt-1 flex items-center gap-1 text-xs text-yellow-600 hover:text-yellow-700 font-medium transition-colors"
                    >
                        <i class="fas fa-star text-[10px]"></i> Rate
                    </button>
                    <span v-else-if="props.order.status === 'Delivered' && row.rate" class="text-xs text-gray-400 flex items-center gap-1 mt-1">
                        <i class="fas fa-check text-[10px] text-green-500"></i> Rated
                    </span>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex items-center justify-between gap-2 flex-wrap">
                <button
                    v-if="props.order.status === 'Pending'"
                    @click="onCancel"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium border border-red-200 transition-colors"
                >
                    <i class="fas fa-times text-xs"></i> Cancel Order
                </button>
                <div v-else></div>
                <button
                    @click="showOrderModal = false"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                >
                    Close
                </button>
            </div>
        </template>
    </el-dialog>

    <!-- Rating dialog -->
    <el-dialog
        width="min(460px, 95%)"
        v-model="reviewModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-star text-yellow-500 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Leave a Review</h2>
                    <p class="text-xs text-gray-400">Share your experience with this product</p>
                </div>
            </div>
        </template>

        <!-- Stars -->
        <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">
                Rating <span class="text-red-500">*</span>
            </label>
            <el-rate
                v-model="rating_form.rate"
                size="large"
                show-score
                text-color="#ff9900"
                score-template="{value} / 5"
            />
            <p v-if="form_errors.value.rate" class="text-red-500 text-xs mt-1">{{ form_errors.value.rate[0] }}</p>
        </div>

        <!-- Comment -->
        <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                Comment <span class="text-red-500">*</span>
            </label>
            <el-input
                v-model="rating_form.comment"
                placeholder="Tell us about your experience..."
                type="textarea"
                :rows="3"
                clearable
            />
            <p v-if="form_errors.value.comment" class="text-red-500 text-xs mt-1">{{ form_errors.value.comment[0] }}</p>
        </div>

        <!-- Upload -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                Photos / Videos <span class="text-gray-400 font-normal normal-case">(optional)</span>
            </label>
            <el-upload
                ref="imgUpload"
                action="#"
                :auto-upload="false"
                :on-change="onChangeImage"
                accept="image/*,video/*"
                :on-remove="handleRemove"
                drag
                multiple
                list-type="picture"
            >
                <div class="flex flex-col items-center py-4">
                    <i class="fas fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Drop files or <em class="text-green-600 not-italic font-medium">click to upload</em></p>
                    <p class="text-xs text-gray-400 mt-1">Max 2MB per file</p>
                </div>
            </el-upload>
            <p v-if="form_errors.value['images.0']" class="text-red-500 text-xs mt-1">{{ form_errors.value['images.0'][0] }}</p>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <button @click="reviewModal = false" class="px-5 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button @click="onSubmitRatings" class="px-5 py-2 rounded-xl text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-paper-plane text-xs"></i> Submit
                </button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import axios from 'axios';
import { ElMessage } from 'element-plus';

const showOrderModal = ref(true);

const props = defineProps({
    onClose: { type: Function, default: () => {} },
    order: { type: Object, default: () => ({}) },
});

watch(showOrderModal, val => { if (!val) props.onClose(); });

const reviewModal = ref(false);
const form_errors = reactive({ value: {} });
const rating_form = reactive({
    rate: 0,
    comment: null,
    order_details_id: null,
    images: [],
});

function addRating(id) {
    form_errors.value = {};
    rating_form.rate = 0;
    rating_form.order_details_id = id;
    rating_form.comment = null;
    reviewModal.value = true;
}

function onCancel() {
    axios.post(route('cart.cancel', props.order.id))
        .then(() => {
            ElMessage.success('Order cancelled successfully');
            props.onClose(true);
        })
        .catch(() => ElMessage.error('Something went wrong'));
}

function onSubmitRatings() {
    if (rating_form.rate === 0) rating_form.rate = null;
    axios.post(route('rating.add'), rating_form, {
        headers: { 'content-type': 'multipart/form-data' },
    })
    .then(() => {
        reviewModal.value = false;
        props.onClose(true);
    })
    .catch(error => { form_errors.value = error.response.data.errors; })
    .finally(() => { if (rating_form.rate == null) rating_form.rate = 0; });
}

function onChangeImage(img) { rating_form.images.push(img.raw); }
function handleRemove(img) { rating_form.images = rating_form.images.filter(i => i !== img.raw); }
function getTotal(d) { return parseInt(d.qty) * parseInt(d.product.price); }

function getStatusBadge(status) {
    if (status === 'Pending') return 'bg-yellow-100 text-yellow-700';
    if (status === 'Packed') return 'bg-blue-100 text-blue-700';
    if (status === 'Out for Delivery') return 'bg-purple-100 text-purple-700';
    if (status === 'Delivered') return 'bg-green-100 text-green-700';
    return 'bg-red-100 text-red-600';
}

function getStatusIcon(status) {
    if (status === 'Pending') return 'fas fa-clock';
    if (status === 'Packed') return 'fas fa-box';
    if (status === 'Out for Delivery') return 'fas fa-truck';
    if (status === 'Delivered') return 'fas fa-check-circle';
    return 'fas fa-times-circle';
}
</script>

<style scoped>
:deep(.el-dialog) { border-radius: 1rem; }
:deep(.el-dialog__header) { padding: 20px 20px 12px; border-bottom: 1px solid #f3f4f6; margin-right: 0; }
:deep(.el-dialog__body) { padding: 20px; }
:deep(.el-dialog__footer) { padding: 12px 20px 20px; border-top: 1px solid #f3f4f6; }
</style>
