<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from "@headlessui/vue";
import { ExclamationTriangleIcon } from "@heroicons/vue/24/outline";
import { ArrowPathIcon } from "@heroicons/vue/24/solid";

defineProps({
  show: Boolean,
  title: String,
  confirmLabel: String,
  danger: Boolean,
  loading: Boolean,
});

const emit = defineEmits(["confirm", "close"]);
</script>

<template>
  <TransitionRoot as="template" :show="show">
    <Dialog as="div" class="relative z-50" @close="emit('close')">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-gray-900 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10">
                  <ExclamationTriangleIcon :class="danger ? 'text-red-600' : 'text-green-600'" class="h-6 w-6" aria-hidden="true" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <DialogTitle as="h3" class="text-base leading-6 font-semibold text-gray-300">{{ title }}</DialogTitle>
                  <div class="mt-2">
                    <p class="text-sm text-gray-400"><slot /></p>
                  </div>
                </div>
              </div>
              <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button
                  type="button"
                  :class="danger ? 'bg-red-600 hover:bg-red-500' : 'bg-green-600 hover:bg-green-500'"
                  class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-xs sm:ml-3 sm:w-auto"
                  @click="emit('confirm')"
                >
                  <ArrowPathIcon v-if="loading" class="h-5 w-5 animate-spin" />
                  <span v-else>{{ confirmLabel }}</span>
                </button>
                <button
                  type="button"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-white/20 sm:mt-0 sm:w-auto"
                  @click="emit('close')"
                >
                  Cancel
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
