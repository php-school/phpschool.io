<script>

import { FolderIcon, FolderOpenIcon, DocumentIcon, PencilIcon, FolderPlusIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline'
import uniqueName from "./utils/uniqueName.js";

export default {
  components: {
    FolderIcon,
    DocumentIcon,
    FolderOpenIcon,
    PencilIcon,
    FolderPlusIcon,
    PlusIcon,
    TrashIcon
  },
  inject: ['state'],
  name: 'tree-item',
  props: {
    parent: Array,
    model: Object,
    deleteFunction: Function,
    fileSelectFunction: Function,
    showControls: {
      type: Boolean,
      default: false
    },
    customStyles: {
      type: Object,
    }
  },
  data() {
    return {
      isOpen: false,
      isEditing: false,
    }
  },
  mounted() {
    if (this.isNew(this.model)) {
      this.$nextTick(() => this.$refs.name.focus());
    }
  },
  computed: {
    isFolder() {
      return this.model.children
    },
    hasChildren() {
      return this.model.children.length;
    },
    // sortedChildren() {
    //   const dirs = this.model.children.filter(child => child.children);
    //   const files = this.model.children.filter(child => !child.children);
    //
    //   const sorter = (a, b) => {
    //     if (a.name.toLowerCase() < b.name.toLowerCase()) {
    //       return -1;
    //     }
    //
    //     if (a.name.toLowerCase() > b.name.toLowerCase()) {
    //       return 1;
    //     }
    //
    //     return 0;
    //   };
    //
    //   return dirs.sort(sorter).concat(files.sort(sorter));
    // },
    isBeingEdited() {
      return this.isEditing || this.isNew(this.model);
    },
  },
  methods: {
    isNew(file) {
      return 'new' in file && file.new === true;
    },
    selectNode(child) {
      if (this.isEditing) {
        return;
      }

      if (this.isFolder) {
        this.isOpen = !this.isOpen
        return;
      }

      this.state.selectedFile = child;

      if (this.fileSelectFunction) {
        this.fileSelectFunction(child);
      }
    },
    edit() {
      this.isEditing = true;
      this.$nextTick(() => this.$refs.name.focus());
    },
    addFile() {
      if (this.model.children.filter(file => 'new' in file).length) {
        return;
      }

      const file = {
        name: uniqueName('new file', this.model.children),
        new: true,
        parent: this.model,
      }

      this.model.children.push(file);

      this.isOpen = true;
    },
    addFolder() {
      if (this.model.children.filter(file => 'new' in file).length) {
        return;
      }

      this.model.children.push({
        name: uniqueName('new folder', this.model.children),
        children: [],
        parent: this.model,
        new: true,
      });

      this.isOpen = true;
    },
    saveName() {
      const names = this.parent
          .filter(child => child !== this.model)
          .map(child => child.name);

      if (names.includes(this.model.name)) {
        alert('Name must be unique');
        return;
      }
      // Limit file name size
      if(this.model.name.length > 14) {
        this.model.name = this.model.name.slice(0,14); 
      }
      if (this.model.new) {
        delete this.model.new;
      }

      this.isEditing = false;
    },
    deleteChild(child) {
      if (!this.deleteFunction) {
        return;
      }

      this.deleteFunction(child)
          .then((shouldDelete) => {
            if (shouldDelete) {
              const index = this.parent.findIndex((elem) => elem === child);
              this.parent.splice(index, 1);
            }
          })

    }
  }
}
</script>

<template>
  <li :class="[model === state.selectedFile ? (customStyles?.selectedFileClasses ?? '') : '']" class="flex flex-col pl-3 py-2 w-full">
    <div @click="selectNode(model)" class="group flex w-full items-center justify-between cursor-pointer">
      <div class="flex items-center min-w-0">
        <FolderIcon v-if="isFolder" v-show="!isOpen || !hasChildren" class="mr-1 h-5 w-5" style="fill: none !important;"/>
        <FolderOpenIcon v-if="isFolder && hasChildren" v-show="isOpen" class="mr-1 h-5 w-5" style="fill: none !important;"/>
        <DocumentIcon v-if="!isFolder" class="mr-1 h-5 w-5" style="fill: none !important;"/>
        <span v-show="!isBeingEdited" class="hover:text-white flex-1 truncate">{{ model.name }}</span>
        <input ref="name" @keyup.enter="saveName" v-show="isBeingEdited" class="bg-gray-700 p-1 rounded-sm" v-model="model.name"/>
        <span class="ml-2 mr-2" v-if="isFolder && hasChildren && !isBeingEdited">[{{ isOpen ? '-' : '+' }}]</span>
      </div>
      <div v-if="showControls" v-show="!isBeingEdited" class="hidden group-hover:flex">
        <PencilIcon @click.stop="edit" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;"/>
        <FolderPlusIcon @click.stop="addFolder" v-if="isFolder" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;"/>
        <PlusIcon @click.stop="addFile" v-if="isFolder" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;"/>
        <TrashIcon @click.stop="deleteChild(model)" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500 fill-none" style="fill: none !important;"/>
      </div>
    </div>

    <div v-show="isOpen" v-if="isFolder" class="ml-3">
      <ul :class="{'mt-1': hasChildren}" class="w-full text-gray-300 ">
        <tree-item v-for="child in model.children"
                   :parent="model.children"
                   :model="child"
                   :delete-function="deleteFunction"
                   :file-select-function="fileSelectFunction"
                   :custom-styles="customStyles"
                   :show-controls="showControls">
        </tree-item>
      </ul>
    </div>
  </li>
</template>