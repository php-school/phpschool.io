<script>
import { XMarkIcon } from '@heroicons/vue/24/solid'

export default {
  components: {
    XMarkIcon
  },
  props: {
    activeTab: {
      type: Number,
      default: 0
    },
    tabList: {
      type: Array,
      required: true,
    },
    disableClose: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  methods: {
    closeTab(tab) {
      this.$emit('close-tab', tab);
    }
  }
}
</script>

<template>
  <div class="tabs w-full flex flex-col">
    <ul class="tabs list-reset flex justify-start mb-1 flex-none">
      <li v-for="(tab, index) in tabList" :key="index" :class="{ 'border-pink-500' : activeTab === index }"
          @click="activeTab = index"
          class="flex border-solid border-t-2 mr-1 bg-stone-700 inline-block py-2 px-4 text-white hover:text-pink-500 text-xs no-underline items-center">
        <a href="#" class="">
          {{ tab }}
        </a>
        <XMarkIcon v-show="disableClose === false" @click="closeTab(tab)" class="cursor-pointer ml-2 w-3 h-3 text-zinc-400"  />
      </li>
    </ul>
    <template v-for="(tab, index) in tabList">
      <div class="tabs-panel h-full flex flex-1" :key="index" v-if="index === activeTab">
        <slot :name="`tab-content-${index}`" />
      </div>
    </template>
  </div>
</template>