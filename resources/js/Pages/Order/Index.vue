<template>
    <AuthLayout>
        <!-- Page header -->
        <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Order List</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage and track all customer orders</p>
            </div>
        </div>

        <!-- Desktop table -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <el-table :data="data.value" row-key="id" header-row-class-name="thead-light">
                <el-table-column min-width="140px" label="Date">
                    <template v-slot="{ row }">
                        <span class="text-sm text-gray-700">{{ row.date }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="130px" label="Client">
                    <template v-slot="{ row }">
                        <span class="text-sm font-medium text-gray-800">{{ row.name }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="130px" label="Amount">
                    <template v-slot="{ row }">
                        <span class="font-bold text-green-700">₱{{ Number(row.amount).toLocaleString() }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="90px" label="Paid">
                    <template v-slot="{ row }">
                        <span :class="`inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full ${row.paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`">
                            <i :class="row.paid ? 'fas fa-check' : 'fas fa-clock'" class="text-[10px]"></i>
                            {{ row.paid ? 'Paid' : 'Unpaid' }}
                        </span>
                    </template>
                </el-table-column>
                <el-table-column min-width="140px" label="Status">
                    <template v-slot="{ row }">
                        <span :class="`inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full ${getStatusClass(row.status)}`">
                            <i :class="getStatusIcon(row.status)" class="text-[10px]"></i>
                            {{ row.status }}
                        </span>
                    </template>
                </el-table-column>
                <el-table-column width="80px" label="Action" fixed="right">
                    <template v-slot="{ row }">
                        <Link :href="route('order.details.show', row.id)">
                            <button class="size-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-colors">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </Link>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end px-4 py-3 border-t border-gray-100">
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
        </div>

        <!-- Mobile cards -->
        <div class="md:hidden space-y-3">
            <div
                v-for="row in data.value"
                :key="row.id"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4"
            >
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ row.name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                            <i class="fas fa-calendar text-[10px]"></i> {{ row.date }}
                        </p>
                    </div>
                    <p class="font-bold text-green-600 text-base">₱{{ Number(row.amount).toLocaleString() }}</p>
                </div>
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex gap-2 flex-wrap">
                        <span :class="`inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full ${row.paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`">
                            <i :class="row.paid ? 'fas fa-check' : 'fas fa-clock'" class="text-[10px]"></i>
                            {{ row.paid ? 'Paid' : 'Unpaid' }}
                        </span>
                        <span :class="`inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full ${getStatusClass(row.status)}`">
                            <i :class="getStatusIcon(row.status)" class="text-[10px]"></i>
                            {{ row.status }}
                        </span>
                    </div>
                    <Link :href="route('order.details.show', row.id)">
                        <button class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium transition-colors">
                            <i class="fas fa-eye text-[10px]"></i> View
                        </button>
                    </Link>
                </div>
            </div>

            <!-- Mobile pagination -->
            <div class="flex justify-center pt-2">
                <el-pagination
                    v-model:currentPage="pagination.page"
                    v-model:page-size="pagination.page_size"
                    background
                    layout="prev, pager, next"
                    :total="pagination_total"
                    @current-change="fetchData"
                    small
                />
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { onBeforeMount, reactive, ref } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';

const pagination = reactive({ search: null, page_size: 10, page: 1 });
const pagination_total = ref(0);
const data = reactive({ value: [] });

onBeforeMount(fetchData);

function getTotal(details) {
    return details.reduce((t, d) => t + parseInt(d.qty) * parseInt(d.product.price), 0);
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

function fetchData() {
    axios.get(route('order.get', pagination)).then(res => {
        data.value = res.data.data.map(d => ({
            id: d.id,
            name: d.user.name,
            date: d.created_at.replace('T', ' ').substring(0, 10),
            paid: d.paid,
            status: d.status,
            amount: getTotal(d.details),
        }));
        pagination_total.value = res.data.total;
    });
}
</script>
