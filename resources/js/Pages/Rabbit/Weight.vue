<template>
    <AuthLayout>
        <div class="space-y-6">

            <!-- Page header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Weight Tracker</h1>
                <p class="text-sm text-gray-500 mt-0.5">Monitor and log your rabbits' weight over time</p>
            </div>

            <!-- Top section: gauge + form -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Current weight gauge -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 flex flex-col items-center justify-center">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-6">Current Weight</p>

                    <!-- Circular gauge -->
                    <div class="relative size-52">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#f0fdf4" stroke-width="8"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#16a34a" stroke-width="8"
                                stroke-linecap="round"
                                :stroke-dasharray="`${gaugePercent} ${264 - gaugePercent}`"
                                stroke-dashoffset="0"
                                class="transition-all duration-700"
                            />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-4xl font-extrabold text-gray-800">{{ last_weight }}</span>
                            <span class="text-sm text-gray-400 mt-1">kg</span>
                        </div>
                    </div>

                    <div v-if="form_data.rabbit_id" class="mt-6 text-center">
                        <div class="text-sm font-medium text-gray-700">
                            {{ selectedRabbitName }}
                        </div>
                        <div v-if="form_data.breed" class="text-xs text-gray-400 mt-0.5">{{ form_data.breed }}</div>
                    </div>
                    <div v-else class="mt-4 text-xs text-gray-400">Select a rabbit to track</div>
                </div>

                <!-- Log weight form -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-green-600 text-sm"></i>
                        Log New Weight
                    </h3>

                    <div class="space-y-4">
                        <!-- Weight input -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Weight (kg)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                    <i class="fas fa-weight-scale"></i>
                                </span>
                                <input
                                    v-model="form_data.weight"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="e.g. 2.5"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all"
                                >
                            </div>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date</label>
                            <el-date-picker
                                v-model="form_data.date"
                                placeholder="Select date"
                                type="date"
                                clearable
                                style="width: 100%;"
                            />
                            <p v-if="form_errors.value.date" class="text-red-500 text-xs mt-1">
                                {{ form_errors.value.date[0] }}
                            </p>
                        </div>

                        <!-- Breed -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Rabbit Breed</label>
                            <el-select
                                v-model="form_data.breed"
                                placeholder="Select breed"
                                filterable
                                clearable
                                style="width: 100%;"
                                @change="fetchRabbits"
                            >
                                <el-option v-for="b in breeds" :key="b" :value="b" :label="b" />
                            </el-select>
                        </div>

                        <!-- Rabbit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Rabbit</label>
                            <el-select
                                v-model="form_data.rabbit_id"
                                placeholder="Select rabbit"
                                filterable
                                clearable
                                style="width: 100%;"
                                @change="fetchData"
                            >
                                <el-option
                                    v-for="rabbit in data.rabbits"
                                    :key="rabbit.id"
                                    :value="rabbit.id"
                                    :label="rabbit.name"
                                />
                            </el-select>
                        </div>

                        <!-- Submit -->
                        <button
                            @click="onAddWeight"
                            class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-plus text-xs"></i> Add Weight Entry
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chart + history (only when rabbit selected) -->
            <template v-if="form_data.rabbit_id">

                <!-- Line chart -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-line text-green-600 text-sm"></i>
                        Weight Progress
                    </h3>
                    <LineChart
                        class="w-full"
                        :data="chartData"
                        :options="chartOptions"
                    />
                </div>

                <!-- Weight history table -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-history text-green-600 text-sm"></i>
                            Weight History
                        </h3>
                        <span class="text-xs text-gray-400">{{ data.weights.length }} entries</span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div
                            v-for="(weight, i) in data.weights"
                            :key="i"
                            class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-lg bg-green-50 flex items-center justify-center">
                                    <i class="fas fa-weight-scale text-green-600 text-xs"></i>
                                </div>
                                <span class="font-semibold text-gray-800 text-sm">{{ weight }} kg</span>
                            </div>
                            <span class="text-xs text-gray-400 italic">{{ data.dates[i] }}</span>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { onBeforeMount, reactive, ref, computed } from 'vue';
import axios from 'axios';
import { Chart, registerables } from 'chart.js';
import { Line as LineChart } from 'vue-chartjs';

Chart.register(...registerables);

const data = reactive({ dates: [], weights: [], rabbits: [] });
const last_weight = ref(0);
const form_errors = reactive({ value: {} });
const form_data = reactive({
    breed: null,
    rabbit_id: null,
    date: null,
    weight: 0,
});

const breeds = ['Californian', 'Lion Head', 'New Zealand', 'Teddy Rabbit'];

const selectedRabbitName = computed(() => {
    const r = data.rabbits.find(r => r.id === form_data.rabbit_id);
    return r?.name ?? '';
});

// Gauge: map weight 0–5kg to 0–264 (circumference)
const gaugePercent = computed(() => {
    const max = 5;
    const pct = Math.min(Number(last_weight.value) / max, 1);
    return Math.round(pct * 264);
});

const chartData = computed(() => ({
    labels: data.dates,
    datasets: [{
        label: 'Weight (kg)',
        data: data.weights,
        borderColor: '#16a34a',
        backgroundColor: 'rgba(22,163,74,0.1)',
        borderWidth: 2,
        pointBackgroundColor: '#16a34a',
        pointRadius: 4,
        tension: 0.4,
        fill: true,
    }],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: { legend: { display: false } },
    scales: {
        y: { beginAtZero: false, grid: { color: '#f3f4f6' } },
        x: { grid: { display: false } },
    },
};

function fetchRabbits() {
    axios.get(route('rabbit.dropdown', { breed: form_data.breed }))
        .then(res => { data.rabbits = res.data; });
}

function fetchData() {
    if (!form_data.rabbit_id) {
        data.dates = [];
        data.weights = [];
        return;
    }
    axios.get(route('rabbit.weight.get', form_data.rabbit_id))
        .then(res => {
            data.dates = res.data.dates.map(v => v.replace('T', ' ').substring(0, 10));
            data.weights = res.data.weights;
            if (data.weights.length > 0)
                last_weight.value = data.weights[data.weights.length - 1];
        });
}

function onAddWeight() {
    axios.post(route('rabbit.weight.add', form_data))
        .then(() => {
            last_weight.value = form_data.weight;
            form_data.weight = 0;
            form_data.date = null;
            fetchData();
        });
}
</script>
