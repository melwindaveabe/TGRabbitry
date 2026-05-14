<template>
    <AuthLayout>
        <div class="flex justify-between flex-wrap mb-5">
            <div class="text-lg font-semibold">Summary</div>
        </div>

        <el-card shadow="always" class="mt-2">
            <div class="flex items-center gap-2">
                <div class="rotate-text text-black">
                    Total
                </div>
                <div class="w-full" style="height: 400px;">
                    <canvas ref="chartRef"></canvas>
                </div>
            </div>
        </el-card>

    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Chart, registerables } from 'chart.js';
import { ref, onMounted } from 'vue';

Chart.register(...registerables);

const props = defineProps(['data', 'title']);
const chartRef = ref(null);

onMounted(() => {
    if (!chartRef.value || !props.data || !props.title) return;

    new Chart(chartRef.value, {
        type: 'bar',
        data: {
            labels: props.title,
            datasets: [
                {
                    label: 'Summary',
                    data: props.data,
                    backgroundColor: [
                        'green', 'cyan', '#263fcc', 'yellow', 'lime',
                        'gray', 'blue', 'orange', 'purple', 'pink', 'black', 'teal'
                    ],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true },
            },
            scales: {
                y: { beginAtZero: true }
            }
        },
    });
});
</script>
