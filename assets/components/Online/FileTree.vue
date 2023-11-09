<script setup>

import {computed, provide, ref} from 'vue'
import TreeItem from "./TreeItem.vue";
import { FolderPlusIcon, PlusIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import uniqueName from "./Utils/uniqueName.js";

const props = defineProps({
  deleteFunction: Function,
  fileSelectFunction: Function,
  initialSelectedItem: Object,
  showControls: {
    type: Boolean,
    default: false
  },
  files: Array,
  customStyles: Object
});

const emit = defineEmits(['reset']);

const state = ref({
  state: {
    selectedFile: props.initialSelectedItem
  }
})

provide('state', computed(() => state.value))

const reset = () => {
  emit('reset');
};

const addFile = () => {
  if (props.files.some(file => 'new' in file)) {
    return;
  }

  const file = {
    name: uniqueName('new file', props.files),
    new: true,
    parent: null,
  };

  props.files.push(file);
};

const addFolder = () => {
  if (props.files.some(file => 'new' in file)) {
    return;
  }

  props.files.push({
    name: uniqueName('new folder', props.files),
    children: [],
    parent: null,
    new: true,
  });
};
</script>

<template>
  <div class="">
    <div class="border-b border-solid border-gray-600 py-5 px-3  flex justify-between">
      <span class="text-white text-base font-mono">Files</span>
      <div v-if="showControls"  class="flex text-white">
        <XMarkIcon @click="reset" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;"/>
        <FolderPlusIcon @click="addFolder" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;"/>
        <PlusIcon @click="addFile" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;"/>
      </div>
    </div>
    <ul class="w-full text-gray-300 font-mono">
      <tree-item
          v-for="file in files"
          :parent="files"
          :model="file"
          :delete-function="deleteFunction"
          :file-select-function="fileSelectFunction"
          :custom-styles="customStyles"
          :show-controls="showControls">
      </tree-item>
    </ul>
  </div>
</template>