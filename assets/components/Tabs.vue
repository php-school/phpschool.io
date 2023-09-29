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
  <div class="tabs flex flex-1 flex-col">
    <ul class="tabs list-reset flex justify-start flex-none  border-solid border-gray-600">
      <li v-for="(tab, index) in tabList" :key="index"
          @click="activeTab = index"
          class="flex justify-between border-solid border-r border-r-gray-600  pl-2 pr-1 inline-block text-white hover:text-pink-500 text-xs no-underline items-center"
          :class="[activeTab === index ? 'border-t -mt-[1px] border-t-pink-600' : 'border-b border-b-gray-600']">
        <a href="#" class="text-gray-300 hover:no-underline py-6 pl-1 " :class="[activeTab === index ? 'text-pink-600' : '']">
          {{ tab }}
        </a>
        <XMarkIcon v-show="disableClose === false" @click.stop="closeTab(tab)" class="cursor-pointer ml-2 mr-1 w-3 h-3 text-zinc-400 hover:text-white"  />
      </li>
      <li class="flex-1 border-b border-solid border-gray-600"></li>
    </ul>
    <template v-for="(tab, index) in tabList">
      <div class="tabs-panel h-full flex flex-1" :key="index" v-if="index === activeTab">
        <slot :name="`tab-content-${index}`" />
      </div>
    </template>
  </div>
</template>