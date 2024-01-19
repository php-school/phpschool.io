<script setup>
import { computed, provide, ref } from "vue";
import TreeItem from "./TreeItem.vue";
import { FolderPlusIcon, PlusIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import uniqueName from "./Utils/uniqueName.js";

const props = defineProps({
  deleteFunction: Function,
  fileSelectFunction: Function,
  initialSelectedItem: Object,
  showControls: {
    type: Boolean,
    default: false,
  },
  files: Array,
  customStyles: Object,
});

const emit = defineEmits(["reset", "add-file", "delete-file", "rename-file"]);

const state = ref({
  state: {
    selectedFile: props.initialSelectedItem,
  },
});

provide(
  "state",
  computed(() => state.value),
);

const reset = () => {
  emit("reset");
};

const fileAdded = (file) => {
  emit("add-file", file);
};

const fileDeleted = (file) => {
  emit("delete-file", file);
};

const fileRenamed = (file) => {
  emit("rename-file", file);
};

const addFile = () => {
  if (props.files.some((file) => "new" in file)) {
    return;
  }

  const file = {
    name: uniqueName("new file", props.files),
    new: true,
    parent: null,
  };

  emit("add-file", { parent: props.files, file: file });
};

const addFolder = () => {
  if (props.files.some((file) => "new" in file)) {
    return;
  }

  const folder = {
    name: uniqueName("new folder", props.files),
    children: [],
    parent: null,
    new: true,
  };

  emit("add-file", { parent: props.files, file: folder });
};
</script>

<template>
  <div class="">
    <div class="flex justify-between border-b border-solid border-gray-600 px-3 py-5">
      <span class="font-mono text-base text-white">Files</span>
      <div v-if="showControls" class="flex text-white">
        <XMarkIcon
          @click="reset"
          class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500"
          style="fill: none !important"
        />
        <FolderPlusIcon
          @click="addFolder"
          class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500"
          style="fill: none !important"
        />
        <PlusIcon
          @click="addFile"
          class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500"
          style="fill: none !important"
        />
      </div>
    </div>
    <ul class="w-full font-mono text-gray-300">
      <TreeItem
        v-for="file in files"
        :key="file.name"
        :parent="files"
        :model="file"
        :delete-function="deleteFunction"
        :file-select-function="fileSelectFunction"
        :custom-styles="customStyles"
        :show-controls="showControls"
        @add-file="fileAdded"
        @delete-file="fileDeleted"
        @rename-file="fileRenamed"
      ></TreeItem>
    </ul>
  </div>
</template>
