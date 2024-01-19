<script setup>
import Code from "../../DocCode.vue";

defineProps({
  event: String,
  eventClass: String,
  args: Array,
});
</script>

<template>
  <div class="">
    <h3 class="mb-4 font-bold">{{ event }}</h3>
    <p class="mb-6 text-xs">
      Event Class:
      <Code>{{ eventClass }}</Code>
    </p>

    <p class="mb-8"><slot></slot></p>

    <template v-if="args.length > 0">
      <p class="text-xs font-semibold">Arguments:</p>
      <dl class="mb-4 w-full p-2">
        <div v-for="arg in args" :key="arg.name" class="border-b py-2 last:border-none sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
          <dt class="text-xs italic">{{ arg.name }}</dt>
          <dd class="text-xs sm:col-span-2">
            <Code>{{ arg.type }}</Code>
          </dd>
        </div>
      </dl>
    </template>
    <p v-else class="">Arguments - None</p>
  </div>
</template>
