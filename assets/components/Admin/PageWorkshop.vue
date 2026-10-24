<script setup>
import { onMounted, ref } from "vue";
import { getWorkshop } from "./api";
import InstallsChart from "./InstallsChart.vue";

const props = defineProps({
  search: {
    type: String,
    default: "",
  },
  id: {
    type: String,
    default: null,
  },
});

const workshop = ref({ code: "Loading...", name: "Loading..." });

const chartData = ref({
  labels: [],
  datasets: [],
});

onMounted(async () => {
  const data = await getWorkshop(props.id);

  workshop.value = data.workshop;

  chartData.value = {
    labels: data.graphData.dates,
    datasets: [
      {
        label: "# of Installs",
        data: data.graphData.data,
      },
    ],
  };
});

const types = {
  Core: "text-green-400 bg-green-400/10 ring-green-400/30",
  Community: "text-sky-400 bg-sky-400/10 ring-sky-500/30",
};

const rows = [
  ["Workshop Code", "code"],
  ["Workshop Name", "name"],
  ["Workshop Description", "description"],
  ["Workshop Type", "type"],
  ["Submitter Name", "submitter_name"],
  ["Submitter email", "submitter_email"],
  ["Submitter contact", "submitter_contact"],
];
</script>

<template>
  <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
    <h1 class="text-base leading-7 font-semibold text-white">
      {{ workshop.name }}
    </h1>
  </header>

  <div class="">
    <dl class="divide-y divide-white/10">
      <div v-for="[label, key] in rows" :key="key" class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0 sm:px-6 lg:px-8">
        <dt class="text-sm leading-6 font-medium text-white">{{ label }}</dt>
        <dd v-if="key === 'type'" class="mt-1 flex text-sm leading-6 text-gray-400 sm:col-span-2 sm:mt-0">
          <div :class="[types[workshop.type], 'flex-none rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset']">
            {{ workshop.type }}
          </div>
        </dd>
        <dd v-else class="mt-1 text-sm leading-6 text-gray-400 sm:col-span-2 sm:mt-0">
          {{ workshop[key] }}
        </dd>
      </div>
    </dl>
  </div>

  <InstallsChart :data="chartData" />
</template>
