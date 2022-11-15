<script>

import { XMarkIcon } from '@heroicons/vue/24/solid'

export default {
  components: {
    XMarkIcon
  },
  props: {
    size: {
      type: String,
      default: '2xl',
    },
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
      }
    }
  },
  methods: {
    closeModal(child) {
      this.$emit('close');
    },
  }
}
</script>

<template>
  <div class="">
    <div class="bg-gray-900 bg-opacity-80 fixed inset-0 z-40"/>
    <div
        tabindex="-1"
        class="overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center flex"
    >
      <div class="relative p-4 w-full max-h-screen" :class="modalSizeClasses[size]">
        <div class="relative max-h-full rounded-lg shadow bg-gray-800">
          <div class="p-4 rounded-t flex justify-between items-center"
               :class="$slots.header ? 'border-b border-solid border-gray-600' : ''">
            <slot name="header"/>
            <div>
              <button @click="closeModal" type="button"
                      class="text-gray-400 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center hover:bg-gray-600 hover:text-white">
                <XMarkIcon class="w-5 h-5"/>
              </button>
            </div>
          </div>
          <div class="p-6 max-h-full overflow-y-auto" :class="$slots.header ? '' : 'pt-0'">
            <slot name="body"/>
          </div>
          <div v-if="$slots.footer" class="p-6 rounded-b border-t border-gray-600">
            <slot name="footer"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>