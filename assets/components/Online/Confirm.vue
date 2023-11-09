<script setup>
import { ExclamationTriangleIcon } from "@heroicons/vue/24/solid";
import {nextTick, ref} from "vue";

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
  if (evt.code === 'Escape') {
    confirm();
  }
}

const onEscapeKeyDecline = (evt) => {
  if (evt.code === 'Escape') {
    decline();
  }
}

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
  if(options.okMessage) {
    okMessage.value = options.okMessage;
  }
  if(options.cancelMessage) {
    cancelMessage.value = options.cancelMessage;
  }

  if (options.disableCancel) {
    showCancel.value = false;
    escapeHandler = onEscapeKeyConfirm;
  } else {
    //hitting escape is the same as cancelling
    escapeHandler = onEscapeKeyDecline;
  }

  document.addEventListener('keyup', escapeHandler);

  open();

  nextTick(() => {
    focusActive.value = true;
  });

  return new Promise((resolve,reject) => {
    resolvePromise.value = resolve;
    rejectPromise.value = reject;
  });
}

const confirm = () => {
  close();
  resolvePromise.value(true);
}

const decline = () => {
  close();
  resolvePromise.value(false);
}

defineExpose({
  show,
});
</script>

<template >
  <focus-trap v-model:active="focusActive" :initial-focus="() => confirmButton">
    <div v-show="isOpen" class="fixed rounded-lg bg-gray-800 flex flex-col justify-start w-full h-full inset-0 z-40 bg-opacity-80">
      <div class="bg-grey-800 h-full w-full flex justify-center items-center drop-shadow-xl">
        <div class="bg-gray-900 max-w-sm max-h-[calc(1/2*100%)] rounded-lg">
          <div class="p-8 rounded-t flex-none flex justify-between items-top border-b border-solid border-slate-600">
            <div class="flex items-center">
              <ExclamationTriangleIcon class="h-6 w-6 text-pink-500 mr-2" />
              <h3 class="font-mono text-base font-semibold lg:text-lg text-white pt-0 mt-0">{{ title }}</h3>
            </div>
          </div>
          <div class="p-8 rounded-t flex-none flex justify-between items-top border-b border-solid border-slate-600">
            <div class="flex items-center">
              <p class="text-white text-sm">{{ message }}</p>
            </div>
          </div>
          <div class="p-6 rounded-b border-solid border-slate-600 flex-none">
            <div class="flex justify-end">
              <button v-if="showCancel" @click="decline" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-pink-600 px-8 py-2 text-base font-medium text-gray-400 hover:text-white shadow-sm hover:bg-pink-600 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm focus:outline-none focus:ring focus:ring-pink-800">{{ cancelMessage }}</button>
              <button ref="confirmButton" @click="confirm" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm focus:outline-none focus:ring focus:ring-pink-800">{{ okMessage }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </focus-trap>
</template>


