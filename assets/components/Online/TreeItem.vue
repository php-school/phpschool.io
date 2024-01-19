<script setup>
import { FolderIcon, FolderOpenIcon, DocumentIcon, PencilIcon, FolderPlusIcon, PlusIcon, TrashIcon } from "@heroicons/vue/24/outline";
import uniqueName from "./Utils/uniqueName.js";
import Alert from "./ConfirmDialog.vue";
import { computed, inject, nextTick, onMounted, ref, watch } from "vue";

const props = defineProps({
  parent: Array,
  model: Object,
  deleteFunction: Function,
  fileSelectFunction: Function,
  showControls: {
    type: Boolean,
    default: false,
  },
  customStyles: Object,
});

const emit = defineEmits(["add-file", "delete-file", "rename-file"]);

const state = inject("state");

const isOpen = ref(false);
const isEditing = ref(false);

const fileName = ref(props.model.name);
const name = ref(null);
const fileAlert = ref(null);

onMounted(() => {
  if (isNew(props.model)) {
    nextTick(() => name.value.focus());
  }
});

watch(
  () => props.model.name,
  (newVal) => {
    fileName.value = newVal;
  },
);

const isNew = (file) => {
  return "new" in file && file.new === true;
};

const isFolder = computed(() => {
  return props.model.children;
});

const hasChildren = computed(() => {
  return props.model.children.length;
});

const isBeingEdited = computed(() => {
  return isEditing.value || isNew(props.model);
});

const selectNode = (child) => {
  if (isEditing.value) {
    return;
  }

  if (isFolder.value) {
    isOpen.value = !isOpen.value;
    return;
  }

  state.selectedFile = child;

  if (props.fileSelectFunction) {
    props.fileSelectFunction(child);
  }
};

const edit = () => {
  isEditing.value = true;
  nextTick(() => name.value.focus());
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
  if (props.model.children.filter((file) => "new" in file).length) {
    return;
  }

  const file = {
    name: uniqueName("new file", props.model.children),
    new: true,
    parent: props.model,
  };

  emit("add-file", { parent: props.model.children, file: file });

  isOpen.value = true;
};

const addFolder = () => {
  if (props.model.children.filter((file) => "new" in file).length) {
    return;
  }

  const file = {
    name: uniqueName("new folder", props.model.children),
    children: [],
    parent: props.model,
    new: true,
  };

  emit("add-file", { parent: props.model.children, file: file });

  isOpen.value = true;
};

const saveName = async () => {
  const names = props.parent.filter((child) => child !== props.model).map((child) => child.name);

  if (names.includes(fileName.value)) {
    await fileAlert.value.show({
      title: "Error",
      message: "The filename must be unique.",
      okMessage: "OK",
      disableCancel: true,
    });
    return;
  }

  let regex = /^[a-zA-Z0-9_ -]+\.[a-zA-Z0-9]+$/;
  if (isFolder.value) {
    regex = /^[a-zA-Z0-9_ -]+$/;
  }

  if (!fileName.value.match(regex)) {
    await fileAlert.value.show({
      title: "Error",
      message: "Files and folder name must contain only alphanumerics, dashes, underscores and spaces. Additionally, files must have an extension.",
      okMessage: "OK",
      disableCancel: true,
    });
    return;
  }

  // Limit file name size
  if (fileName.value.length > 14) {
    fileName.value = fileName.value.slice(0, 14);
  }

  emit("rename-file", {
    file: props.model,
    name: fileName.value,
  });

  isEditing.value = false;
};

const deleteChild = async (child) => {
  if (!props.deleteFunction) {
    return;
  }

  const result = await props.deleteFunction(child);

  if (result) {
    emit("delete-file", {
      parent: props.parent,
      index: props.parent.findIndex((elem) => elem === child),
    });
  }
};
</script>

<template>
  <alert ref="fileAlert" />
  <li :class="[model === state.selectedFile ? customStyles?.selectedFileClasses ?? '' : '']" class="flex w-full flex-col py-2 pl-3">
    <div @click="selectNode(model)" class="group flex w-full cursor-pointer items-center justify-between">
      <div class="flex min-w-0 items-center">
        <FolderIcon v-if="isFolder" v-show="!isOpen || !hasChildren" class="mr-1 h-5 w-5" style="fill: none !important" />
        <FolderOpenIcon v-if="isFolder && hasChildren" v-show="isOpen" class="mr-1 h-5 w-5" style="fill: none !important" />
        <DocumentIcon v-if="!isFolder" class="mr-1 h-5 w-5" style="fill: none !important" />
        <span v-show="!isBeingEdited" class="flex-1 truncate text-sm hover:text-white">{{ model.name }}</span>
        <input ref="name" @keyup.enter="saveName" v-show="isBeingEdited" class="rounded-sm bg-gray-700 p-1" v-model="fileName" />
        <span class="ml-2 mr-2 text-sm" v-if="isFolder && hasChildren && !isBeingEdited">[{{ isOpen ? "-" : "+" }}]</span>
      </div>
      <div v-if="showControls" v-show="!isBeingEdited" class="hidden group-hover:flex">
        <PencilIcon @click.stop="edit" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important" />
        <FolderPlusIcon @click.stop="addFolder" v-if="isFolder" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important" />
        <PlusIcon @click.stop="addFile" v-if="isFolder" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important" />
        <TrashIcon @click.stop="deleteChild(model)" class="mr-2 h-5 w-5 cursor-pointer fill-none hover:text-pink-500" style="fill: none !important" />
      </div>
    </div>

    <div v-show="isOpen" v-if="isFolder" class="ml-3">
      <ul :class="{ 'mt-1': hasChildren }" class="w-full text-gray-300">
        <tree-item
          v-for="child in model.children"
          :key="child.name"
          :parent="model.children"
          :model="child"
          :delete-function="deleteFunction"
          :file-select-function="fileSelectFunction"
          :custom-styles="customStyles"
          :show-controls="showControls"
          @add-file="fileAdded"
          @delete-file="fileDeleted"
          @rename-file="fileRenamed"
        ></tree-item>
      </ul>
    </div>
  </li>
</template>
