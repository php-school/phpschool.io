<script>

import { computed } from 'vue'
import TreeItem from "./TreeItem.vue";
import { FolderPlusIcon, PlusIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import uniqueName from "./utils/uniqueName.js";

export default {
  components: {
    TreeItem,
    FolderPlusIcon,
    PlusIcon,
    XMarkIcon
  },
  props: {
    deleteFunction: Function,
    fileSelectFunction: Function,
    initialSelectedItem: Object,
    showControls: {
      type: Boolean,
      default: false
    },
    files: Array,
    customStyles: {
      type: Object,
    }
  },
  provide() {
    return {
      state: computed(() => this.state)
    }
  },
  data() {
    return {
      state: {
        selectedFile: this.initialSelectedItem
      }
    }
  },
  methods: {
    reset() {
      this.$emit('reset');
    },
    addFile() {
      if (this.files.filter(file => 'new' in file).length) {
        return;
      }

      const file = {
        name: uniqueName('new file', this.files),
        new: true,
        parent: null,
      }

      this.files.push(file);
    },
    addFolder() {
      if (this.files.filter(file => 'new' in file).length) {
        return;
      }

      this.files.push({
        name: uniqueName('new folder', this.files),
        children: [],
        parent: null,
        new: true,
      });
    },
  }
}
</script>

<template>
  <div class="border border-solid border-gray-600 rounded">
    <div class="border-b border-solid border-gray-600 p-3 flex justify-between">
      <span class="text-white text-base font-mono">Files</span>
      <div v-if="showControls"  class="flex text-white">
        <XMarkIcon @click="reset" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;"/>
        <FolderPlusIcon @click="addFolder" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;"/>
        <PlusIcon @click="addFile" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;"/>
      </div>
    </div>
    <ul class="w-full text-gray-300 font-mono p-1">
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