<template>
    <GuestLayout>

        <div class="max-w-4xl mx-auto px-4 py-10">

            <!-- Profile card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">

                    <!-- Cover banner -->
                <div class="h-36 bg-gradient-to-r from-green-700 to-green-400 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-20"
                        style="background-image: url('/images/website/m.jpg'); background-size: cover; background-position: center;">
                    </div>
                    <!-- Decorative circles -->
                    <div class="absolute -top-6 -right-6 size-32 bg-white/10 rounded-full"></div>
                    <div class="absolute -bottom-8 right-24 size-24 bg-white/10 rounded-full"></div>
                </div>

                <!-- Avatar row — sits below the banner, no overlap -->
                <div class="px-6 pt-4 pb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                        <!-- Avatar + name -->
                        <div class="flex items-center gap-4">
                            <img
                                loading="lazy"
                                :src="`${$page.props.base_img_path}${$page.props.user.image_name}`"
                                class="size-20 rounded-2xl object-cover border-4 border-white shadow-lg ring-2 ring-green-200 -mt-10"
                                alt=""
                            >
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ $page.props.user.name }}</h2>
                                <p class="text-sm text-gray-500 flex items-center gap-1.5 mt-1">
                                    <i class="fas fa-envelope text-xs text-green-500"></i>
                                    {{ $page.props.user.email }}
                                </p>
                                <p v-if="$page.props.user.city" class="text-sm text-gray-500 flex items-center gap-1.5 mt-0.5">
                                    <i class="fas fa-location-dot text-xs text-green-500"></i>
                                    {{ $page.props.user.city }}
                                </p>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex flex-wrap gap-2">
                            <button
                                @click="showProfileModal = true"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-medium transition-colors shadow-sm"
                            >
                                <i class="fas fa-user-pen text-xs"></i> Edit Profile
                            </button>
                            <button
                                @click="showPasswordModal = true"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition-colors"
                            >
                                <i class="fas fa-lock text-xs"></i> Password
                            </button>
                            <button
                                @click="logout"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium transition-colors border border-red-200"
                            >
                                <i class="fas fa-power-off text-xs"></i> Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800">My Orders</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Track your purchase history</p>
                    </div>
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                        {{ pagination_total }} order{{ pagination_total !== 1 ? 's' : '' }}
                    </span>
                </div>

                <div class="p-4">
                    <!-- Orders table -->
                    <template v-if="pagination_total > 0">
                        <!-- Desktop table -->
                        <div class="hidden sm:block">
                            <el-table
                                :data="data.value"
                                row-key="id"
                                header-row-class-name="thead-light"
                                class="w-full"
                            >
                                <el-table-column min-width="140px" label="Date">
                                    <template v-slot="{ row }">
                                        <span class="text-sm text-gray-700">{{ row.date }}</span>
                                    </template>
                                </el-table-column>

                                <el-table-column min-width="140px" label="Total Amount">
                                    <template v-slot="{ row }">
                                        <span class="font-semibold text-green-700">₱{{ numberWithCommas(row.amount) }}</span>
                                    </template>
                                </el-table-column>

                                <el-table-column min-width="100px" label="Paid">
                                    <template v-slot="{ row }">
                                        <span :class="`inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full
                                            ${row.paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`">
                                            <i :class="row.paid ? 'fas fa-check' : 'fas fa-clock'" class="text-[10px]"></i>
                                            {{ row.paid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    </template>
                                </el-table-column>

                                <el-table-column min-width="150px" label="Status">
                                    <template v-slot="{ row }">
                                        <span :class="`inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full ${getStatusClass(row.status)}`">
                                            <i :class="getStatusIcon(row.status)" class="text-[10px]"></i>
                                            {{ row.status }}
                                        </span>
                                    </template>
                                </el-table-column>

                                <el-table-column width="80px" label="Action" fixed="right">
                                    <template v-slot="{ row }">
                                        <button
                                            @click="showOrderModals(row.id)"
                                            class="size-8 rounded-lg bg-green-50 hover:bg-green-100 text-green-700 flex items-center justify-center transition-colors"
                                            title="View Details"
                                        >
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>

                        <!-- Mobile cards -->
                        <div class="sm:hidden space-y-3">
                            <div
                                v-for="row in data.value"
                                :key="row.id"
                                class="bg-gray-50 rounded-xl p-4 border border-gray-100"
                            >
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <div class="text-xs text-gray-400">{{ row.date }}</div>
                                        <div class="font-bold text-green-700 mt-0.5">₱{{ numberWithCommas(row.amount) }}</div>
                                    </div>
                                    <button
                                        @click="showOrderModals(row.id)"
                                        class="size-8 rounded-lg bg-green-100 text-green-700 flex items-center justify-center"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                </div>
                                <div class="flex gap-2">
                                    <span :class="`inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full
                                        ${row.paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`">
                                        {{ row.paid ? 'Paid' : 'Unpaid' }}
                                    </span>
                                    <span :class="`inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full ${getStatusClass(row.status)}`">
                                        {{ row.status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-end pt-4">
                            <el-pagination
                                v-model:currentPage="pagination.page"
                                v-model:page-size="pagination.page_size"
                                :page-sizes="[5, 10, 50, 100]"
                                background
                                layout="total, sizes, prev, pager, next"
                                :total="pagination_total"
                                @size-change="fetchData"
                                @current-change="fetchData"
                            />
                        </div>
                    </template>

                    <!-- Empty state -->
                    <div v-else class="flex flex-col items-center justify-center py-16 text-gray-400">
                        <i class="fas fa-box-open text-5xl mb-4 opacity-30"></i>
                        <p class="font-medium text-gray-500">No orders yet</p>
                        <p class="text-sm mt-1">Start shopping to see your orders here.</p>
                        <Link :href="route('shop')" class="mt-4 px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors">
                            Browse Shop
                        </Link>
                    </div>
                </div>
            </div>

        </div>
    </GuestLayout>

    <OrderDetailsModal v-if="showOrderModal" :on-close="onCloseOrderDetails" :order="data.order" />
    <UpdateProfile v-if="showProfileModal" :on-close="() => showProfileModal = false" />
    <UpdatePassword v-if="showPasswordModal" :on-close="() => showPasswordModal = false" />
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import axios from 'axios';
import { ref, reactive, onBeforeMount } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UpdateProfile from '@/Components/UpdateProfile.vue';
import UpdatePassword from '@/Components/UpdatePassword.vue';
import OrderDetailsModal from '@/Components/OrderDetailsModal.vue';

const numberWithCommas = (str) => {
    str += '';
    const number = parseFloat(str.replace(/,/g, ''));
    return !isNaN(number) ? number.toLocaleString() : '';
};

const pagination = reactive({ search: null, page_size: 10, page: 1 });
const showProfileModal = ref(false);
const showPasswordModal = ref(false);
const showOrderModal = ref(false);
const pagination_total = ref(0);
const data = reactive({ value: [], order: {} });

onBeforeMount(() => fetchData());

function getTotal(details) {
    let total = 0;
    for (let i in details) {
        const d = details[i];
        total += parseInt(d.qty) * parseInt(d.product.price);
    }
    return total;
}

function getStatusClass(status) {
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

function onCloseOrderDetails(isRefresh) {
    showOrderModal.value = false;
    if (isRefresh) fetchData();
}

function showOrderModals(id) {
    axios.get(route('order.details-user', id))
        .then(res => {
            data.order = res.data;
            showOrderModal.value = true;
        });
}

function fetchData() {
    axios.get(route('order.get-user', pagination))
        .then(res => {
            data.value = res.data.data.map(d => ({
                id: d.id,
                date: d.created_at.replace('T', ' ').substring(0, 10),
                paid: d.paid,
                status: d.status,
                amount: getTotal(d.details),
            }));
            pagination_total.value = res.data.total;
        });
}

function logout() {
    router.post(route('logout'));
}
</script>
