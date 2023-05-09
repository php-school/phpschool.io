<script>

import { XMarkIcon } from '@heroicons/vue/24/solid'

export default {
  components: {
    XMarkIcon
  },
  props: {
    id: {
      type: String,
      default: null
    },
    size: {
      type: String,
      default: '2xl',
    },
    scrollContent: {
      type: Boolean,
      default: false,
    },
    maxHeight: {
      type: String,
      default: null
    }
  },
  mounted() {
    this.$el.escapeEventHandler = (evt) => {
      if (evt.code === 'Escape') {
        this.closeModal();
      }
    };

    document.addEventListener('keyup', this.$el.escapeEventHandler);
  },
  unmounted() {
    document.removeEventListener('keyup', this.$el.escapeEventHandler);
  },
  data() {
    return {
      modalSizeClasses: {
        xs: 'max-w-xs',
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg',
        xl: 'max-w-xl',
        '2xl': 'max-w-2xl',
        '3xl': 'max-w-3xl',
        '4xl': 'max-w-4xl',
        '5xl': 'max-w-5xl',
        '6xl': 'max-w-6xl',
        '7xl': 'max-w-7xl',
      },
      focusActive: false,
    }
  },
  methods: {
    clickAway(e) {
      if (this.shepherd.activeTour) {
        return;
      }

      if (e.target.parentElement && e.target.parentElement.classList.contains('shepherd-cancel-icon')) {
        return;
      }

      this.closeModal(e);
    },

    closeModal($event) {
      this.$emit('close', $event);
    },
  }
}
</script>

<template >
    <focus-trap :v-model:active="true">
        <div class="">
        <div class="bg-gray-900 bg-opacity-80 fixed inset-0 z-40"/>
        <div tabindex="-1" class="overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 md:h-full justify-center items-center flex">
          <div :id="id" v-click-away="clickAway" class="relative rounded-lg shadow bg-gray-800 flex flex-col justify-start w-full modal-border border-2 border-solid border-transparent" :class="[modalSizeClasses[size], maxHeight]">
              <div class="p-6 rounded-t flex-none flex justify-between items-top"
                   :class="$slots.header ? 'border-b border-solid border-slate-600' : ''">
                <slot name="header"/>
                <div>
                  <button :id="id + '-close'" @click="closeModal($event)" type="button"
                          class="text-gray-400 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center hover:bg-gray-600 hover:text-white focus:outline-none focus:ring focus:ring-pink-800">
                    <XMarkIcon class="w-5 h-5"/>
                  </button>
                </div>
              </div>
              <div class="p-6 flex-1" :class="{'pt-0' : !$slots.header, 'overflow-y-auto scrollbar-thin scrollbar-thumb-pink-500 scrollbar-track-none scrollbar-thumb-rounded-full scrollbar-track-rounded-full mr-[3px]' : scrollContent}" >
                <slot name="body"/>
              </div>
              <div v-if="$slots.footer" class="p-6 rounded-b border-t border-solid border-slate-600 flex-none">
                <slot name="footer"/>
              </div>
            </div>
        </div>
      </div>
    </focus-trap>
</template>

<style>
.modal-border {
    background: padding-box linear-gradient(rgb(31 41 55), rgb(31 41 55)),
        border-box linear-gradient(rgb(236, 72, 153), rgba(168, 85, 247, 0.3));
}
</style>