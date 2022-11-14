<script>

import { computed } from 'vue'
import TreeItem from "./TreeItem.vue";

export default {
  components: {
    TreeItem
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
        <svg @click="addFolder" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
        </svg>

        <svg @click="addFile" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
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