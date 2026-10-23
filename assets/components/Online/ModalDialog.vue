<script setup>
import { XMarkIcon } from "@heroicons/vue/24/solid";
import { DialogClose, DialogContent, DialogOverlay, DialogPortal, DialogRoot } from "reka-ui";
import Shepherd from "shepherd.js";

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

//the editor tour highlights elements inside and outside the modal, so clicking them must not close it
const interactOutside = (event) => {
  const target = event.detail?.originalEvent?.target ?? event.target;

  if (Shepherd.activeTour || target?.closest?.(".shepherd-element")) {
    event.preventDefault();
  }
};

//focus the dialog itself rather than the close button, so no focus ring shows until the user tabs
const openAutoFocus = (event) => {
  event.preventDefault();
  event.currentTarget?.focus?.();
};

const onOpenChange = (open) => {
  if (!open) {
    emit("close");
  }
};
</script>

<template>
  <DialogRoot :open="true" @update:open="onOpenChange">
    <DialogPortal>
      <DialogOverlay class="fixed inset-0 z-40 bg-gray-900 bg-opacity-80" />
      <div class="fixed left-0 right-0 top-0 z-50 flex h-full w-full items-center justify-center overflow-x-hidden p-2 md:inset-0 md:p-0">
        <DialogContent
          :id="id"
          :aria-describedby="undefined"
          class="modal-border relative flex w-full flex-col justify-start overflow-hidden rounded-lg border-2 border-solid border-transparent bg-gray-800 shadow focus:outline-none"
          :class="[modalSizeClasses[size], maxHeight]"
          @interact-outside="interactOutside"
          @open-auto-focus="openAutoFocus"
        >
          <div class="items-top flex flex-none justify-between rounded-t p-6" :class="$slots.header ? 'border-b border-solid border-slate-600' : ''">
            <slot name="header" />
            <div>
              <DialogClose
                :id="id + '-close'"
                class="ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-600 hover:text-white focus:outline-none focus:ring focus:ring-pink-800"
              >
                <XMarkIcon class="h-5 w-5" />
              </DialogClose>
            </div>
          </div>
          <div
            class="flex-1"
            :class="[
              !$slots.header ? 'pt-0' : '',
              scrollContent ? 'scrollbar-track-none mr-[3px] overflow-y-auto scrollbar-thin scrollbar-thumb-pink-500 scrollbar-track-rounded-full scrollbar-thumb-rounded-full' : '',
              bodyClasses,
            ]"
          >
            <slot name="body" />
          </div>
          <div v-if="$slots.footer" class="flex-none rounded-b border-t border-solid border-slate-600 p-6">
            <slot name="footer" />
          </div>
        </DialogContent>
      </div>
    </DialogPortal>
  </DialogRoot>
</template>

<style>
.modal-border {
  background:
    padding-box linear-gradient(rgb(31 41 55), rgb(31 41 55)),
    border-box linear-gradient(rgb(236, 72, 153), rgba(168, 85, 247, 0.3));
}
</style>
