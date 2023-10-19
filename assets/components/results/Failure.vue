<script>

import Modal from "../Modal.vue";
import {ExclamationTriangleIcon} from '@heroicons/vue/24/solid'

export default {
  components: {
    Modal,
    ExclamationTriangleIcon
  },
  props: {
    data: Object
  },
  data() {
    return {
      openModal: false,
    }
  },
}
</script>

<template>
  <div class="mt-1 w-full flex justify-between">
    <p class="text-red-500 text-sm">{{ data.reason }}</p>
  </div>
</template>
