<template>
    <AuthLayout>
        <!-- Page header -->
        <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Order Details</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage order #{{ order.id }}</p>
            </div>
            <Link :href="route('order.invoice', order.id)">
                <button class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-file-invoice text-xs"></i> Create Invoice
                </button>
            </Link>
        </div>

        <!-- Order card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <!-- Header info -->
            <div class="bg-gradient-to-r from-green-600 to-green-500 px-5 py-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <p class="text-white/70 text-xs uppercase tracking-wide font-medium">Customer</p>
                        <p class="text-white font-semibold text-base mt-0.5">{{ order.user.name }}</p>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <span :class="`text-xs font-semibold px-3 py-1 rounded-full ${paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`">
                            <i :class="paid ? 'fas fa-check' : 'fas fa-clock'" class="mr-1 text-[10px]"></i>
                            {{ paid ? 'Paid' : 'Unpaid' }}
                        </span>
                        <span :class="`text-xs font-semibold px-3 py-1 rounded-full ${getStatusBadge(status)}`">
                            <i :class="getStatusIcon(status)" class="mr-1 text-[10px]"></i>
                            {{ status }}
                        </span>
                    </div>
                </div>
                <p class="text-white/80 text-sm mt-2 flex items-center gap-1.5">
                    <i class="fas fa-location-dot text-xs"></i>
                    {{ order.address }}
                </p>
            </div>

            <!-- Desktop table -->
            <div class="hidden md:block">
                <el-table :data="order.details" row-key="id" header-row-class-name="thead-light">
                    <el-table-column min-width="60px" label="Qty">
                        <template v-slot="{ row }">
                            <span class="font-semibold text-gray-700">{{ row.qty }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column min-width="180px" label="Product">
                        <template v-slot="{ row }">
                            <span class="text-sm font-medium text-gray-800">{{ row.product.name }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column min-width="100px" label="Price">
                        <template v-slot="{ row }">
                            <span class="text-sm text-gray-600">₱{{ Number(row.product.price).toLocaleString() }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column min-width="110px" label="Total">
                        <template v-slot="{ row }">
                            <span class="font-bold text-green-600">₱{{ getTotal(row).toLocaleString() }}</span>
                        </template>
                    </el-table-column>
                </el-table>
            </div>

            <!-- Mobile cards -->
            <div class="md:hidden divide-y divide-gray-50">
                <div
                    v-for="row in order.details"
                    :key="row.id"
                    class="flex items-center gap-4 px-5 py-4"
                >
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">{{ row.product.name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            ₱{{ Number(row.product.price).toLocaleString() }} × {{ row.qty }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-bold text-green-600 text-sm">₱{{ getTotal(row).toLocaleString() }}</p>
                    </div>
                </div>
            </div>

            <!-- Footer actions -->
            <div class="bg-gray-50 border-t border-gray-100 px-5 py-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        <p class="flex items-center gap-2">
                            <span class="text-gray-500">Payment Method:</span>
                            <span class="font-semibold text-gray-800">{{ order.payment_method }}</span>
                        </p>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <button
                            v-if="!paid"
                            @click="onMarkAsPaid"
                            class="flex items-center gap-2 px-4 py-2 bg-green-50 hover:bg-green-100 text-green-600 text-sm font-medium rounded-xl border border-green-200 transition-colors"
                        >
                            <i class="fas fa-check text-xs"></i> Mark as Paid
                        </button>
                        <button
                            @click="showModal = true"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm"
                        >
                            <i class="fas fa-edit text-xs"></i> Update Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>

    <!-- Update status dialog -->
    <el-dialog
        width="min(420px, 95%)"
        v-model="showModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-edit text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Update Order Status</h2>
                    <p class="text-xs text-gray-400">Change the current order status</p>
                </div>
            </div>
        </template>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                Status <span class="text-red-500">*</span>
            </label>
            <el-select class="w-full" v-model="status" filterable placeholder="Select status">
                <el-option
                    v-for="st in ['Pending', 'Packed', 'Out for Delivery', 'Delivered', 'Cancelled']"
                    :key="st"
                    :label="st"
                    :value="st"
                >
                    <span class="flex items-center gap-2">
                        <i :class="getStatusIcon(st)" class="text-xs"></i>
                        {{ st }}
                    </span>
                </el-option>
            </el-select>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <button
                    @click="onCancel"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="onSubmit"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors flex items-center gap-2"
                >
                    <i class="fas fa-check text-xs"></i> Update
                </button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { ref } from 'vue';
import axios from 'axios';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Link } from '@inertiajs/vue3';

const { order } = defineProps(['order']);

const showModal = ref(false);
const status = ref(order.status);
const paid = ref(order.paid);

function getTotal(d) {
    return parseInt(d.qty) * parseInt(d.product.price);
}

function onCancel() {
    status.value = order.status;
    showModal.value = false;
}

function getStatusBadge(st) {
    if (st === 'Pending') return 'bg-yellow-100 text-yellow-700';
    if (st === 'Packed') return 'bg-blue-100 text-blue-700';
    if (st === 'Out for Delivery') return 'bg-purple-100 text-purple-700';
    if (st === 'Delivered') return 'bg-green-100 text-green-700';
    return 'bg-red-100 text-red-600';
}

function getStatusIcon(st) {
    if (st === 'Pending') return 'fas fa-clock';
    if (st === 'Packed') return 'fas fa-box';
    if (st === 'Out for Delivery') return 'fas fa-truck';
    if (st === 'Delivered') return 'fas fa-check-circle';
    return 'fas fa-times-circle';
}

function getTagType(st) {
    if (st === 'Pending') return 'warning';
    if (st === 'Packed') return 'primary';
    if (st === 'Out for Delivery') return 'info';
    if (st === 'Delivered') return 'success';
    return 'danger';
}

function onMarkAsPaid() {
    ElMessageBox.confirm(
        'Are you sure you want to mark this order as paid?',
        'Confirm Payment',
        {
            confirmButtonText: 'Yes, Mark as Paid',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
    .then(async () => {
        await axios.post(route('order.mark-as-paid', order.id))
            .then(() => {
                paid.value = 1;
                order.paid = 1;
                ElMessage({ type: 'success', message: 'Marked as paid successfully.' });
            });
    })
    .catch(() => {});
}

function onSubmit() {
    axios.post(route('order.update-status', order.id), { status: status.value })
        .then(() => {
            order.status = status.value;
            showModal.value = false;
            ElMessage({ type: 'success', message: 'Status updated successfully.' });
        });
}
</script>

<style scoped>
:deep(.el-dialog) { border-radius: 1rem; }
:deep(.el-dialog__header) { padding: 20px 20px 12px; border-bottom: 1px solid #f3f4f6; margin-right: 0; }
:deep(.el-dialog__body) { padding: 20px; }
:deep(.el-dialog__footer) { padding: 12px 20px 20px; border-top: 1px solid #f3f4f6; }
:deep(.el-select) { width: 100%; }
</style>
