<script setup>
import { ExclamationTriangleIcon } from "@heroicons/vue/24/solid";
import { AlertDialogContent, AlertDialogDescription, AlertDialogPortal, AlertDialogRoot, AlertDialogTitle } from "reka-ui";
import { ref } from "vue";

const isOpen = ref(false);
const showCancel = ref(true);
const title = ref(null);
const message = ref(null);
const okMessage = ref("Confirm");
const cancelMessage = ref("Cancel");
const resolvePromise = ref(null);
const confirmButton = ref(null);

const show = (options) => {
  title.value = options.title;
  message.value = options.message;
  if (options.okMessage) {
    okMessage.value = options.okMessage;
  }
  if (options.cancelMessage) {
    cancelMessage.value = options.cancelMessage;
  }

  showCancel.value = !options.disableCancel;
  isOpen.value = true;

  return new Promise((resolve) => {
    resolvePromise.value = resolve;
  });
};

const close = (result) => {
  isOpen.value = false;
  resolvePromise.value(result);
};

const confirm = () => close(true);
const decline = () => close(false);

//hitting escape is the same as cancelling, or confirming when there is nothing to cancel
const onEscape = () => (showCancel.value ? decline() : confirm());

const focusConfirm = (event) => {
  event.preventDefault();
  confirmButton.value?.focus();
};

defineExpose({
  show,
});
</script>

<template>
  <AlertDialogRoot :open="isOpen">
    <AlertDialogPortal>
      <div v-if="isOpen" class="fixed inset-0 z-60 flex h-full w-full flex-col justify-start rounded-lg bg-gray-800/80">
        <div class="bg-grey-800 flex h-full w-full items-center justify-center p-2 drop-shadow-xl md:p-0">
          <AlertDialogContent class="max-h-1/2 max-w-sm rounded-lg bg-gray-900 focus:outline-hidden" @open-auto-focus="focusConfirm" @escape-key-down="onEscape">
            <div class="items-top flex flex-none justify-between rounded-t border-b border-solid border-slate-600 p-8">
              <div class="flex items-center">
                <ExclamationTriangleIcon class="mr-2 h-6 w-6 text-pink-500" />
                <AlertDialogTitle as="h3" class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-lg">
                  {{ title }}
                </AlertDialogTitle>
              </div>
            </div>
            <div class="items-top flex flex-none justify-between rounded-t border-b border-solid border-slate-600 p-8">
              <div class="flex items-center">
                <AlertDialogDescription as="p" class="text-sm text-white">{{ message }}</AlertDialogDescription>
              </div>
            </div>
            <div class="flex-none rounded-b border-solid border-slate-600 p-6">
              <div class="flex justify-end gap-x-2">
                <button
                  v-if="showCancel"
                  @click="decline"
                  type="button"
                  class="inline-flex w-full items-center justify-center rounded-full border border-pink-600 px-8 py-2 text-sm font-medium text-gray-400 shadow-xs hover:bg-pink-600 hover:text-white focus:ring-3 focus:ring-pink-800 focus:outline-hidden sm:ml-3 sm:w-auto md:text-base"
                >
                  {{ cancelMessage }}
                </button>
                <button
                  ref="confirmButton"
                  @click="confirm"
                  type="button"
                  class="inline-flex w-full items-center justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-sm font-medium text-white shadow-xs hover:bg-pink-700 focus:ring-3 focus:ring-pink-800 focus:outline-hidden sm:ml-3 sm:w-auto md:text-base"
                >
                  {{ okMessage }}
                </button>
              </div>
            </div>
          </AlertDialogContent>
        </div>
      </div>
    </AlertDialogPortal>
  </AlertDialogRoot>
</template>
