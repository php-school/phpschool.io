<template>
  <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
    <h1 class="text-base leading-7 font-semibold text-white">Workshops Installs for the last 30 days</h1>
  </header>

  <InstallsChart :data="chartData" />
</template>

<script setup>
import { useAsyncState } from "@vueuse/core";

import InstallsChart from "./InstallsChart.vue";
import { allWorkshops } from "./api";

defineProps({
  search: {
    type: String,
    default: "",
  },
});

const { state: chartData } = useAsyncState(
  async () => {
    const { workshopInstalls } = await allWorkshops();

    return {
      labels: workshopInstalls.dates,
      datasets: workshopInstalls.installs.map((workshop) => ({
        label: workshop.name,
        data: workshop.installs,
        fill: false,
        tension: 0.5,
        borderWidth: 5,
      })),
    };
  },
  { labels: [], datasets: [] },
);
</script>
