<script>
import { ExclamationTriangleIcon } from "@heroicons/vue/24/solid";

export default {
  components: {
    ExclamationTriangleIcon,
  },
  data: () => ({
    focusActive: false,
    isOpen: false,
    title: null,
    message: null,
    okMessage: "Confirm",
    cancelMessage: "Cancel",
    resolvePromise: null,
    rejectPromise: null,
    showCancel: true,
  }),
  methods: {
    open() {
      this.isOpen = true;
    },
    close() {
      document.removeEventListener('keyup', this.$el.escapeEventHandler);

      this.isOpen = false;
      this.focusActive = false;
      this.showCancel = true;
    },
    show(options = {}) {

      this.title = options.title;
      this.message = options.message;
      if(options.okMessage) {
        this.okMessage = options.okMessage;
      }
      if(options.cancelMessage) {
        this.cancelMessage = options.cancelMessage;
      }

      if (options.disableCancel) {
        this.showCancel = false;
        this.$el.escapeEventHandler = (evt) => {
          if (evt.code === 'Escape') {
            this.confirm();
          }
        };
      } else {
        //hitting escape is the same as cancelling
        this.$el.escapeEventHandler = (evt) => {
          if (evt.code === 'Escape') {
            this.decline();
          }
        };
      }

      document.addEventListener('keyup', this.$el.escapeEventHandler);

      this.open();

      this.$nextTick(() => {
        this.focusActive = true;
      });

      return new Promise((resolve,reject) => {
        this.resolvePromise = resolve;
        this.rejectPromise = reject;
      });
    },
    confirm() {
      this.close();
      this.resolvePromise(true);
    },
    decline() {
      this.close();
      this.resolvePromise(false);
    },
  },
};
</script>

<template>
  <focus-trap v-model:active="focusActive" :initial-focus="() => $refs.confirm">
    <div v-show="isOpen" class="fixed rounded-lg bg-gray-800 flex flex-col justify-start w-full h-full inset-0 z-40 bg-opacity-80">
      <div class="bg-grey-800 h-full w-full flex justify-center items-center drop-shadow-xl">
        <div class="bg-gray-900 max-w-sm max-h-[calc(1/2*100%)] rounded-lg">
          <div class="p-4 rounded-t flex-none flex justify-between items-top border-b border-solid border-slate-600">
            <div class="flex items-center">
              <ExclamationTriangleIcon class="h-6 w-6 text-pink-500 mr-2" />
              <h3 class="text-base font-semibold lg:text-lg text-white pt-0 mt-0">{{ title }}</h3>
            </div>
          </div>
          <div class="p-4 rounded-t flex-none flex justify-between items-top border-b border-solid border-slate-600">
            <div class="flex items-center">
              <p class="text-white">{{ message }}</p>
            </div>
          </div>
          <div class="p-6 rounded-b border-solid border-slate-600 flex-none">
            <div class="flex justify-end">
              <button v-if="showCancel" @click="decline" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-pink-600 px-8 py-2 text-base font-medium text-gray-400 hover:text-white shadow-sm hover:bg-pink-600 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm focus:outline-none focus:ring focus:ring-pink-800">{{ cancelMessage }}</button>
              <button ref="confirm" @click="confirm" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm focus:outline-none focus:ring focus:ring-pink-800">{{ okMessage }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </focus-trap>
</template>


