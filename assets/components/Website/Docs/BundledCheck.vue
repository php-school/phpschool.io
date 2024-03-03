<script setup>
import Note from "./DocNote.vue";
import Code from "./DocCode.vue";

defineProps({
  check: String,
  interfaceToImplement: String,
  type: {
    type: String,
    default: "Simple",
  },
  compatible: {
    type: Array,
    validator(value) {
       return value.every((v) => ["CLI", "CGI"].includes(v));
    },
    default: () => ["CLI", "CGI"],
  },
  registered: {
    type: Boolean,
    default: true,
  },
  link: String,
});
</script>

<template>
  <h3 class="mb-4 font-bold">{{ check }}</h3>
  <dl class="mb-4 w-full p-2">
    <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
      <dt class="text-xs italic">Interface to implement:</dt>
      <dd class="text-xs sm:col-span-2">
        <Code>{{ interfaceToImplement }}</Code>
      </dd>
    </div>
    <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
      <dt class="text-xs italic">Type:</dt>
      <dd class="text-xs sm:col-span-2">
        <Code>{{ type }}</Code>
      </dd>
    </div>
    <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
      <dt class="text-xs italic">Compatible Exercise Types:</dt>
      <dd class="text-xs sm:col-span-2">
        <Code>{{ compatible.join(", ") }}</Code>
      </dd>
    </div>
  </dl>

  <p class="mb-6"><slot></slot></p>

  <Note v-if="registered" type="success">Note: You do not need to require this check yourself, it is done so automatically.</Note>
  <a v-if="link" :href="link">Learn how to use</a>
</template>
