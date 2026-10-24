<script setup>
defineProps({
  id: String,
  label: String,
  hint: String,
  errors: Array,
});
</script>

<template>
  <div class="col-span-full">
    <label :for="id" class="block text-sm leading-6 font-medium text-white">{{ label }}</label>
    <div class="mt-2">
      <slot />
    </div>
    <p v-if="hint" class="mt-3 text-sm leading-6 text-gray-400">{{ hint }}</p>
    <ul v-if="errors">
      <li v-for="error in errors" :key="error" class="mt-2 text-xs text-red-600">
        {{ error }}
      </li>
    </ul>
  </div>
</template>
