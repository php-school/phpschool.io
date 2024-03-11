<script setup>
import { ChatBubbleLeftRightIcon } from "@heroicons/vue/24/solid";
import Modal from "../Online/ModalDialog.vue";
import { onMounted, ref } from "vue";

const { open } = defineProps({
  open: Boolean,
});

const emit = defineEmits(["close"]);

const contributors = ref([]);
onMounted(async () => {
  const response = await fetch("/api/contributors");
  contributors.value = await response.json();
});
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-100 ease-in"
      leave-active-class="transition-opacity duration-200 ease-in"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <Modal :scroll-content="true" size="md" max-height="max-h-[calc(5/6*100%)]" v-if="open" @close="emit('close')">
        <template #header>
          <div class="flex items-center">
            <ChatBubbleLeftRightIcon class="mr-2 h-6 w-6 text-pink-500" />
            <h3 class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-xl">Contributors</h3>
          </div>
        </template>
        <template #body>
          <p class="mb-4 text-justify text-sm italic text-white">The PHP School project is made possible by the following contributors. Thank you for your hard work and dedication to the project!</p>
          <ul role="list" class="divide-y divide-gray-800">
            <li v-for="contributor in contributors" :key="contributor.username" class="flex justify-between gap-x-6 py-2">
              <div class="flex min-w-0 gap-x-3">
                <img class="h-8 w-8 flex-none rounded-full bg-gray-800" :src="contributor.profilePic" alt="" />
                <div class="min-w-0 flex-auto">
                  <a :href="contributor.profile" target="_blank" class="text-sm leading-6 text-white">{{ contributor.username }}</a>
                </div>
              </div>
              <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                <p class="mt-1 text-xs leading-5 text-pink-500">
                  <b>{{ contributor.contributions }}</b>
                  Commit{{ contributor.contributions > 1 ? "s" : "" }}
                </p>
              </div>
            </li>
          </ul>
        </template>
      </Modal>
    </Transition>
  </Teleport>
</template>
