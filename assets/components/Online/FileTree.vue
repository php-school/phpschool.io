<script setup>
import { nextTick, ref, watch } from "vue";
import { TreeItem, TreeRoot } from "reka-ui";
import { DocumentIcon, FolderIcon, FolderOpenIcon, FolderPlusIcon, PencilIcon, PlusIcon, TrashIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import uniqueName from "./Utils/uniqueName.js";
import Alert from "./ConfirmDialog.vue";

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

// Reka copies the selected value, so selection is tracked here to keep object identity.
const selectedFile = ref(props.initialSelectedItem);
const expanded = ref([]);
const editing = ref(null);
const fileName = ref("");
const nameInput = ref(null);
const fileAlert = ref(null);

const keys = new WeakMap();
let nextKey = 0;
const keyOf = (file) => keys.get(file) ?? keys.set(file, nextKey++).get(file);
// Match the spacing the old nested lists had: a tighter gap above an open folder's children and extra space after them.
const rowStyle = (item, next) => ({
  marginLeft: (item.level - 1) * 1.5 + "rem",
  paddingBottom: next?.level > item.level ? "0.25rem" : 0.5 + (item.level - (next?.level ?? 1)) * 0.5 + "rem",
});

const isFolder = (file) => Array.isArray(file.children);
const siblingsOf = (file) => (file.parent ? file.parent.children : props.files);

const startEditing = (file) => {
  editing.value = file;
  fileName.value = file.name;
  nextTick(() => nameInput.value?.[0]?.focus());
};

const addTo = (folder, file) => {
  const siblings = folder ? folder.children : props.files;

  if (siblings.some((child) => "new" in child)) {
    return;
  }

  emit("add-file", {
    parent: siblings,
    file: { ...file, name: uniqueName(file.name, siblings), parent: folder, new: true },
  });

  if (folder && !expanded.value.includes(folder)) {
    expanded.value = [...expanded.value, folder];
  }
};

const addFile = (folder = null) => addTo(folder, { name: "new file" });
const addFolder = (folder = null) => addTo(folder, { name: "new folder", children: [] });

watch(
  () => props.files,
  (files) => {
    const find = (list) => list.reduce((found, file) => found ?? (file.new === true ? file : isFolder(file) ? find(file.children) : null), null);
    const file = find(files);
    if (file && editing.value !== file) {
      startEditing(file);
    }
  },
  { deep: true },
);

const onSelect = (event, file) => {
  event.preventDefault();

  if (editing.value || isFolder(file)) {
    return;
  }

  selectedFile.value = file;
  props.fileSelectFunction?.(file);
};

const onToggle = (event) => {
  if (editing.value) {
    event.preventDefault();
  }
};

// Enter and Space only select, so open and close folders on them too.
const onKeydown = (event, file) => {
  if (event.target === event.currentTarget && isFolder(file) && !editing.value) {
    expanded.value = expanded.value.includes(file) ? expanded.value.filter((open) => open !== file) : [...expanded.value, file];
  }
};

const saveName = async () => {
  const file = editing.value;
  const names = siblingsOf(file)
    .filter((child) => child !== file)
    .map((child) => child.name);

  if (names.includes(fileName.value)) {
    await fileAlert.value.show({
      title: "Error",
      message: "The filename must be unique.",
      okMessage: "OK",
      disableCancel: true,
    });
    return;
  }

  const regex = isFolder(file) ? /^[a-zA-Z0-9_ -]+$/ : /^[a-zA-Z0-9_ -]+\.[a-zA-Z0-9]+$/;

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
  emit("rename-file", { file, name: fileName.value.slice(0, 14) });

  editing.value = null;
};

const deleteChild = async (file) => {
  if (!props.deleteFunction || !(await props.deleteFunction(file))) {
    return;
  }

  const siblings = siblingsOf(file);
  emit("delete-file", { parent: siblings, index: siblings.indexOf(file) });
};
</script>

<template>
  <alert ref="fileAlert" />
  <div class="w-full">
    <div class="flex flex-row items-center justify-between border-b border-solid border-gray-600 px-3 py-5">
      <span class="font-mono text-base text-white">Files</span>
      <div v-if="showControls" class="flex text-white">
        <XMarkIcon @click="emit('reset')" class="mr-1 h-4 w-4 cursor-pointer hover:text-pink-500 sm:mr-2 sm:h-5 sm:w-5" style="fill: none !important" />
        <FolderPlusIcon @click="addFolder()" class="mr-1 h-4 w-4 cursor-pointer hover:text-pink-500 sm:mr-2 sm:h-5 sm:w-5" style="fill: none !important" />
        <PlusIcon @click="addFile()" class="mr-1 h-4 w-4 cursor-pointer hover:text-pink-500 sm:mr-2 sm:h-5 sm:w-5" style="fill: none !important" />
      </div>
    </div>
    <TreeRoot v-slot="{ flattenItems }" v-model:expanded="expanded" :items="files" :get-key="(file) => file" class="w-full font-mono text-gray-300">
      <TreeItem
        v-for="(item, index) in flattenItems"
        :key="keyOf(item.value)"
        v-bind="item.bind"
        v-slot="{ isExpanded }"
        :class="[item.value === selectedFile ? (customStyles?.selectedFileClasses ?? '') : '']"
        :style="rowStyle(item, flattenItems[index + 1])"
        class="flex flex-col py-2 pl-3 focus:outline-none focus-visible:ring-1 focus-visible:ring-gray-500"
        @select="(event) => onSelect(event, item.value)"
        @toggle="onToggle"
        @keydown.enter.space="(event) => onKeydown(event, item.value)"
      >
        <div class="group flex w-full cursor-pointer items-center justify-between">
          <div class="flex min-w-0 items-center">
            <FolderOpenIcon v-if="isFolder(item.value) && item.value.children.length && isExpanded" class="mr-1 h-3.5 w-3.5 md:h-5 md:w-5" style="fill: none !important" />
            <FolderIcon v-else-if="isFolder(item.value)" class="mr-1 h-3.5 w-3.5 md:h-5 md:w-5" style="fill: none !important" />
            <DocumentIcon v-else class="mr-1 h-3.5 w-3.5 md:h-5 md:w-5" style="fill: none !important" />
            <input v-if="editing === item.value" ref="nameInput" @keyup.enter="saveName" class="rounded-sm bg-gray-700 p-1" v-model="fileName" />
            <template v-else>
              <span class="flex-1 truncate text-sm hover:text-white">{{ item.value.name }}</span>
              <span class="ml-2 mr-2 text-sm" v-if="isFolder(item.value) && item.value.children.length">[{{ isExpanded ? "-" : "+" }}]</span>
            </template>
          </div>
          <div v-if="showControls && editing !== item.value" class="flex group-hover:flex md:hidden">
            <PencilIcon @click.stop="startEditing(item.value)" class="mr-2 h-3.5 w-3.5 cursor-pointer hover:text-pink-500 md:h-5 md:w-5" style="fill: none !important" />
            <FolderPlusIcon @click.stop="addFolder(item.value)" v-if="isFolder(item.value)" class="mr-2 h-3.5 w-3.5 cursor-pointer hover:text-pink-500 md:h-5 md:w-5" style="fill: none !important" />
            <PlusIcon @click.stop="addFile(item.value)" v-if="isFolder(item.value)" class="mr-2 h-3.5 w-3.5 cursor-pointer hover:text-pink-500 md:h-5 md:w-5" style="fill: none !important" />
            <TrashIcon @click.stop="deleteChild(item.value)" class="mr-2 h-3.5 w-3.5 cursor-pointer fill-none hover:text-pink-500 md:h-5 md:w-5" style="fill: none !important" />
          </div>
        </div>
      </TreeItem>
    </TreeRoot>
  </div>
</template>
