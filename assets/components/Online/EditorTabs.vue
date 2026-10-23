<script setup>
import { XMarkIcon } from "@heroicons/vue/24/solid";
import { TabsContent, TabsList, TabsRoot, TabsTrigger } from "reka-ui";

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
  <TabsRoot :model-value="activeTab" @update:model-value="changeTab" class="tabs flex flex-1 flex-col">
    <TabsList as="ul" class="tabs list-reset flex flex-none justify-start">
      <TabsTrigger
        v-for="(tab, index) in tabList"
        :key="index"
        :value="index"
        as="li"
        class="flex cursor-pointer items-center justify-between border-r border-solid border-r-gray-600 pr-1 pl-2 text-xs text-white no-underline hover:text-pink-500 focus:outline-hidden"
        :class="[activeTab === index ? 'border-t border-t-pink-600' : 'border-t border-b border-gray-600']"
      >
        <span class="py-6 pl-1 text-gray-300 hover:no-underline" :class="[activeTab === index ? 'text-pink-600' : '']">
          {{ tab }}
        </span>
        <XMarkIcon v-show="disableClose === false" @mousedown.stop @click.stop="closeTab(tab)" class="mr-1 ml-2 h-3 w-3 cursor-pointer text-zinc-400 hover:text-white" />
      </TabsTrigger>
      <li class="flex-1 border-t border-b border-solid border-gray-600"></li>
    </TabsList>
    <TabsContent v-for="(tab, index) in tabList" :key="index" :value="index" class="tabs-panel flex h-full flex-1">
      <slot :name="`tab-content-${index}`" />
    </TabsContent>
  </TabsRoot>
</template>
