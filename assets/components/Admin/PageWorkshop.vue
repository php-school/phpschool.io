<script setup>
import { onMounted, ref } from 'vue'
import { getWorkshop } from './api'

const props = defineProps({
    search: {
        type: String,
        default: ''
    },
    id: {
        type: String,
        default: null
    }
})

const workshop = ref({ code: 'Loading...', name: 'Loading...' })

const chartData = ref({
    labels: [],
    datasets: []
})

onMounted(async () => {
    const data = await getWorkshop(props.id)

    workshop.value = data.workshop

    chartData.value = {
        labels: data.graphData.dates,
        datasets: [
            {
                label: '# of Installs',
                data: data.graphData.data
            }
        ]
    }
})

const types = {
    Core: 'text-green-400 bg-green-400/10 ring-green-400/30',
    Community: 'text-sky-400 bg-sky-400/10 ring-sky-500/30'
}

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

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Colors
)

const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    scales: {
        y: {
            beginAtZero: true
        }
    }
}
</script>

<template>
    <header
        class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8"
    >
        <h1 class="text-base font-semibold leading-7 text-white">
            {{ workshop.name }}
        </h1>
    </header>

    <div class="">
        <dl class="divide-y divide-white/10">
            <div class="px-4 py-6 sm:px-6 lg:px-8 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-white">Workshop Code</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-400 sm:col-span-2 sm:mt-0">
                    {{ workshop.code }}
                </dd>
            </div>
            <div class="px-4 py-6 sm:px-6 lg:px-8 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-white">Workshop Name</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-400 sm:col-span-2 sm:mt-0">
                    {{ workshop.name }}
                </dd>
            </div>
            <div class="px-4 py-6 sm:px-6 lg:px-8 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-white">Workshop Description</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-400 sm:col-span-2 sm:mt-0">
                    {{ workshop.description }}
                </dd>
            </div>
            <div class="px-4 py-6 sm:px-6 lg:px-8 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-white">Workshop Type</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-400 sm:col-span-2 sm:mt-0 flex">
                    <div
                        :class="[
                            types[workshop.type],
                            'rounded-full flex-none py-0.5 px-2 text-[10px] font-medium ring-1 ring-inset'
                        ]"
                    >
                        {{ workshop.type }}
                    </div>
                </dd>
            </div>
            <div class="px-4 py-6 sm:px-6 lg:px-8 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-white">Submitter Name</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-400 sm:col-span-2 sm:mt-0">
                    {{ workshop.submitter_name }}
                </dd>
            </div>
            <div class="px-4 py-6 sm:px-6 lg:px-8 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-white">Submitter email</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-400 sm:col-span-2 sm:mt-0">
                    {{ workshop.submitter_email }}
                </dd>
            </div>
            <div class="px-4 py-6 sm:px-6 lg:px-8 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-white">Submitter contact</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-400 sm:col-span-2 sm:mt-0">
                    {{ workshop.submitter_contact }}
                </dd>
            </div>
        </dl>
    </div>

    <Line class="m-4" :data="chartData" :options="chartOptions" />
</template>
