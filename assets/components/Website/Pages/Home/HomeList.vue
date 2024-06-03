<script setup>
const { list, heading } = defineProps({
  list: Array,
  heading: String,
});
</script>

<template>
  <h2 class="font-work-sans text-5xl font-bold text-white">{{ heading }}</h2>
  <ul class="mt-4">
    <li v-for="line in list" :key="line" class="group">
      <div class="py-2">
        <div class="mr-16 pl-1 text-white">
          <div class="flex items-start text-base text-pink-600 text-nowrap">
            <svg class="ml-1 mr-2 mt-0.5 h-5 w-5 flex-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <p class="text-white"><span class="text-pink-600 font-semibold">{{ line.title }}: </span>{{ line.description }}</p>
          </div>
        </div>
      </div>
    </li>
  </ul>
</template>
