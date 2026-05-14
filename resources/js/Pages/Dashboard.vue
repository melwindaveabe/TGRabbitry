<script setup>
import DashDetails from '@/Components/DashDetails.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Chart, registerables } from 'chart.js';
import { Bar as BarChart } from 'vue-chartjs';
import { computed } from 'vue';

const { monthly_expenses, for_sales, for_restings, total_sales, recent_orders, todays_orders, users, ratings, all_budget, todays_budgets, todays_expenses, categories, monthly_income, rabbits } =
    defineProps(['monthly_expenses', 'for_sales', 'for_restings', 'total_sales', 'recent_orders', 'todays_orders', 'users', 'ratings', 'all_budget', 'todays_budgets', 'todays_expenses', 'categories', 'monthly_income', 'rabbits']);

Chart.register(...registerables);

const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: { display: false },
    },
    scales: {
        y: { beginAtZero: true },
    },
};

const salesChartData = computed(() => ({
    labels: months,
    datasets: [{
        label: 'Monthly Sales',
        data: monthly_income ?? [],
        backgroundColor: '#16a34a',
        borderRadius: 4,
    }],
}));

const expensesChartData = computed(() => ({
    labels: months,
    datasets: [{
        label: 'Monthly Expenses',
        data: monthly_expenses ?? [],
        backgroundColor: '#06b6d4',
        borderRadius: 4,
    }],
}));
</script>

<template>
    <AuthLayout>
        <div class="space-y-5">

            <!-- Page title -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-sm text-gray-500 mt-0.5">Welcome back! Here's what's happening.</p>
            </div>

            <!-- Sales stat cards -->
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Sales Overview</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <DashDetails
                        icon="fas fa-sack-dollar"
                        title="Total Sales"
                        :value="`₱${total_sales.toLocaleString()}`"
                        icon-color="green"
                    />
                    <DashDetails
                        icon="fas fa-clipboard-list"
                        title="Recent Orders"
                        :value="recent_orders"
                        icon-color="blue"
                    />
                    <DashDetails
                        icon="fas fa-cart-plus"
                        title="Today's Orders"
                        :value="todays_orders"
                        icon-color="yellow"
                    />
                    <DashDetails
                        icon="fas fa-users"
                        title="Customers"
                        :value="users"
                        icon-color="purple"
                    />
                    <DashDetails
                        icon="fas fa-comment-dots"
                        title="Reviews"
                        :value="ratings"
                        icon-color="cyan"
                    />
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="text-sm font-semibold text-gray-700 mb-4">Monthly Sales</div>
                    <BarChart :data="salesChartData" :options="chartOptions" />
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="text-sm font-semibold text-gray-700 mb-4">Monthly Expenses</div>
                    <BarChart :data="expensesChartData" :options="chartOptions" />
                </div>
            </div>

            <!-- Budget cards -->
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Budget Overview</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <DashDetails
                        icon="fas fa-coins"
                        title="Overall Budget"
                        :value="`₱${all_budget.toLocaleString()}`"
                        icon-color="green"
                    />
                    <DashDetails
                        icon="fas fa-arrow-up"
                        title="Today's Budget Entries"
                        :value="`₱${todays_budgets.toLocaleString()}`"
                        icon-color="blue"
                    />
                    <DashDetails
                        icon="fas fa-arrow-down"
                        title="Today's Expenses"
                        :value="`₱${todays_expenses.toLocaleString()}`"
                        icon-color="red"
                    />
                    <DashDetails
                        v-for="c in categories"
                        :key="c.id"
                        icon="fas fa-wallet"
                        :title="c.name"
                        :value="`₱${c.bal.toLocaleString()}`"
                        icon-color="purple"
                    />
                </div>
            </div>

        </div>
    </AuthLayout>
</template>