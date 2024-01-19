<script setup>
import { XMarkIcon } from "@heroicons/vue/24/solid";
import Shepherd from "shepherd.js";
import { onMounted, onUnmounted } from "vue";

defineProps({
  id: String,
  size: {
    type: String,
    default: "2xl",
  },
  scrollContent: {
    type: Boolean,
    default: false,
  },
  maxHeight: {
    type: String,
    default: null,
  },
  bodyClasses: {
    type: String,
    default: "p-6",
  },
});

const emit = defineEmits(["close"]);

const modalSizeClasses = {
  xs: "max-w-xs",
  sm: "max-w-sm",
  md: "max-w-md",
  lg: "max-w-lg",
  xl: "max-w-xl",
  "2xl": "max-w-2xl",
  "3xl": "max-w-3xl",
  "4xl": "max-w-4xl",
  "5xl": "max-w-5xl",
  "6xl": "max-w-6xl",
  "7xl": "max-w-7xl",
};

const escapeHandler = (evt) => {
  if (evt.code === "Escape") {
    closeModal();
  }
};

onMounted(() => {
  document.addEventListener("keyup", escapeHandler);
});

onUnmounted(() => {
  document.removeEventListener("keyup", escapeHandler);
});

const clickAway = (e) => {
  if (Shepherd.activeTour) {
    return;
  }

  if (e.target.parentElement && e.target.parentElement.classList.contains("shepherd-cancel-icon")) {
    return;
  }

  closeModal(e);
};

const closeModal = (event) => {
  emit("close", event);
};
</script>

<template>
  <focus-trap :v-model:active="true">
    <div class="">
      <div class="fixed inset-0 z-40 bg-gray-900 bg-opacity-80" />
      <div
        tabindex="-1"
        class="fixed left-0 right-0 top-0 z-50 flex w-full items-center justify-center overflow-x-hidden md:inset-0 md:h-full"
      >
        <div
          :id="id"
          v-click-away="clickAway"
          class="modal-border relative flex w-full flex-col justify-start overflow-hidden rounded-lg border-2 border-solid border-transparent bg-gray-800 shadow"
          :class="[modalSizeClasses[size], maxHeight]"
        >
          <div
            class="items-top flex flex-none justify-between rounded-t p-6"
            :class="$slots.header ? 'border-b border-solid border-slate-600' : ''"
          >
            <slot name="header" />
            <div>
              <button
                :id="id + '-close'"
                @click="closeModal($event)"
                type="button"
                class="ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-600 hover:text-white focus:outline-none focus:ring focus:ring-pink-800"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
          <div
            class="flex-1"
            :class="[
              !$slots.header ? 'pt-0' : '',
              scrollContent
                ? 'scrollbar-track-none mr-[3px] overflow-y-auto scrollbar-thin scrollbar-thumb-pink-500 scrollbar-track-rounded-full scrollbar-thumb-rounded-full'
                : '',
              bodyClasses,
            ]"
          >
            <slot name="body" />
          </div>
          <div v-if="$slots.footer" class="flex-none rounded-b border-t border-solid border-slate-600 p-6">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </div>
  </focus-trap>
</template>

<style>
.modal-border {
  background:
    padding-box linear-gradient(rgb(31 41 55), rgb(31 41 55)),
    border-box linear-gradient(rgb(236, 72, 153), rgba(168, 85, 247, 0.3));
}
</style>
