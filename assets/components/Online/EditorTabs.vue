<script setup>
import { XMarkIcon } from "@heroicons/vue/24/solid";

defineProps({
  activeTab: {
    type: Number,
    default: 0,
  },
  tabList: {
    type: Array,
    required: true,
  },
  disableClose: {
    type: Boolean,
    required: false,
    default: false,
  },
});

const emit = defineEmits(["close-tab", "change-tab"]);

const closeTab = (tab) => {
  emit("close-tab", tab);
};

const changeTab = (tab) => {
  emit("change-tab", tab);
};
</script>

<template>
  <div class="tabs flex flex-1 flex-col">
    <ul class="tabs list-reset flex flex-none justify-start">
      <li
        v-for="(tab, index) in tabList"
        :key="index"
        @click="changeTab(index)"
        class="inline-block flex items-center justify-between border-r border-solid border-r-gray-600 pl-2 pr-1 text-xs text-white no-underline hover:text-pink-500"
        :class="[activeTab === index ? 'border-t border-t-pink-600' : 'border-b border-t border-gray-600 ']"
      >
        <a href="#" class="py-6 pl-1 text-gray-300 hover:no-underline" :class="[activeTab === index ? 'text-pink-600' : '']">
          {{ tab }}
        </a>
        <XMarkIcon v-show="disableClose === false" @click.stop="closeTab(tab)" class="ml-2 mr-1 h-3 w-3 cursor-pointer text-zinc-400 hover:text-white" />
      </li>
      <li class="flex-1 border-b border-t border-solid border-gray-600"></li>
    </ul>
    <template v-for="(tab, index) in tabList">
      <div class="tabs-panel flex h-full flex-1" :key="index" v-if="index === activeTab">
        <slot :name="`tab-content-${index}`" />
      </div>
    </template>
  </div>
</template>
