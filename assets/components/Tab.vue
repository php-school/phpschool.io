<script>
import { inject } from 'vue'

export default {
  props: {
    title: String,
    showCloseButton: {
      type: String,
      default: 'default',
    }
  },
  setup() {
    const selectedTitle = inject("selectedTitle");

    return {
      selectedTitle
    }
  },
}
</script>

<template>
  <div class="tabs-panel h-full flex flex-1" v-show="title === selectedTitle">
    <slot />
  </div>
</template>