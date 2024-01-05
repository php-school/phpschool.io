<template>
    <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
        <h1 class="text-base font-semibold leading-7 text-white">Workshops Installs for the last 30 days</h1>
    </header>

    <Line class="m-4" :data="chartData" :options="chartOptions" />
</template>

<script setup>

import {onMounted, ref} from "vue";

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Colors
} from 'chart.js'

import { Line } from 'vue-chartjs'
import {allWorkshops} from "./api";

const props = defineProps({
    search: {
        type: String,
        default: '',
    }
});

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Colors)

const chartData = ref({
    labels: [],
    datasets: [],
});
const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    scales: {
        y: {
            beginAtZero: true
        }
    }
}

onMounted(async () => {
    const data = await allWorkshops();

    chartData.value = {
        labels: data.workshopInstalls.dates,
        datasets: data.workshopInstalls.installs.map(function (workshop) {
            return {
                label: workshop.name,
                data: workshop.installs,
                fill: false,
                tension: 0.5,
                borderWidth: 5
            };
        })
    }
});
</script>
