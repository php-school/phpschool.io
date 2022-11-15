<script>

import { computed } from 'vue'
import TreeItem from "./TreeItem.vue";
import { FolderPlusIcon, PlusIcon } from '@heroicons/vue/24/outline'

export default {
  components: {
    TreeItem,
    FolderPlusIcon,
    PlusIcon
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
    addFile() {
      if (this.files.filter(file => 'new' in file).length) {
        return;
      }

      this.files.push({
        name: 'new file',
        new: true
      });
    },
    addFolder() {
      if (this.files.filter(file => 'new' in file).length) {
        return;
      }

      this.files.push({
        name: 'new folder',
        children: [],
        new: true
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