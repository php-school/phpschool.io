<script setup>
import { ExclamationTriangleIcon } from "@heroicons/vue/24/solid";
import { nextTick, ref } from "vue";

const focusActive = ref(false);
const isOpen = ref(false);
const showCancel = ref(false);
const title = ref(null);
const message = ref(null);
const okMessage = ref("Confirm");
const cancelMessage = ref("Cancel");
const resolvePromise = ref(null);
const rejectPromise = ref(null);
const confirmButton = ref(null);

const onEscapeKeyConfirm = (evt) => {
  if (evt.code === "Escape") {
    confirm();
  }
};

const onEscapeKeyDecline = (evt) => {
  if (evt.code === "Escape") {
    decline();
  }
};

let escapeHandler = null;

const open = () => {
  isOpen.value = true;
};

const close = () => {
  document.removeEventListener("keyup", escapeHandler);

  isOpen.value = false;
  focusActive.value = false;
  showCancel.value = true;
};

const show = (options) => {
  title.value = options.title;
  message.value = options.message;
  if (options.okMessage) {
    okMessage.value = options.okMessage;
  }
  if (options.cancelMessage) {
    cancelMessage.value = options.cancelMessage;
  }

  if (options.disableCancel) {
    showCancel.value = false;
    escapeHandler = onEscapeKeyConfirm;
  } else {
    //hitting escape is the same as cancelling
    escapeHandler = onEscapeKeyDecline;
  }

  document.addEventListener("keyup", escapeHandler);

  open();

  nextTick(() => {
    focusActive.value = true;
  });

  return new Promise((resolve, reject) => {
    resolvePromise.value = resolve;
    rejectPromise.value = reject;
  });
};

const confirm = () => {
  close();
  resolvePromise.value(true);
};

const decline = () => {
  close();
  resolvePromise.value(false);
};

defineExpose({
  show,
});
</script>

<template>
  <focus-trap v-model:active="focusActive" :initial-focus="() => confirmButton">
    <div v-show="isOpen" class="fixed inset-0 z-40 flex h-full w-full flex-col justify-start rounded-lg bg-gray-800 bg-opacity-80">
      <div class="bg-grey-800 flex h-full w-full items-center justify-center p-2 drop-shadow-xl md:p-0">
        <div class="max-h-[calc(1/2*100%)] max-w-sm rounded-lg bg-gray-900">
          <div class="items-top flex flex-none justify-between rounded-t border-b border-solid border-slate-600 p-8">
            <div class="flex items-center">
              <ExclamationTriangleIcon class="mr-2 h-6 w-6 text-pink-500" />
              <h3 class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-lg">
                {{ title }}
              </h3>
            </div>
          </div>
          <div class="items-top flex flex-none justify-between rounded-t border-b border-solid border-slate-600 p-8">
            <div class="flex items-center">
              <p class="text-sm text-white">{{ message }}</p>
            </div>
          </div>
          <div class="flex-none rounded-b border-solid border-slate-600 p-6">
            <div class="flex justify-end gap-x-2">
              <button
                v-if="showCancel"
                @click="decline"
                type="button"
                class="inline-flex w-full items-center justify-center rounded-full border border-pink-600 px-8 py-2 text-sm font-medium text-gray-400 shadow-sm hover:bg-pink-600 hover:text-white focus:outline-none focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto md:text-base"
              >
                {{ cancelMessage }}
              </button>
              <button
                ref="confirmButton"
                @click="confirm"
                type="button"
                class="inline-flex w-full items-center justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-sm font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto md:text-base"
              >
                {{ okMessage }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </focus-trap>
</template>
