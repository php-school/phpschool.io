<script>
export default {
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
  computed: {
    isFolder() {
      return this.model.children
    },
    hasChildren() {
      return this.model.children.length;
    },
    sortedChildren() {
      const dirs = this.model.children.filter(child => child.children);
      const files = this.model.children.filter(child => !child.children);

      const sorter = (a, b) => {
        if (a.name.toLowerCase() < b.name.toLowerCase()) {
          return -1;
        }

        if (a.name.toLowerCase() > b.name.toLowerCase()) {
          return 1;
        }

        return 0;
      };

      return dirs.sort(sorter).concat(files.sort(sorter));
    },
    isBeingEdited() {
      return this.isEditing || ('new' in this.model && this.model.new);
    },
  },
  methods: {
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
    addFile() {
      if (this.model.children.filter(file => 'new' in file).length) {
        return;
      }

      this.model.children.push({
        name: 'new file',
        new: true
      });

      this.open = true;
    },
    addFolder() {
      if (this.model.children.filter(file => 'new' in file).length) {
        return;
      }

      this.model.children.push({
        name: 'new folder',
        children: [],
        new: true
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

      if (this.model.new) {
        delete this.model.new;
      }

      this.isEditing = false;
    },
    deleteChild(child) {
      const result = this.deleteFunction(child);

      if (result) {
        const index = this.parent.findIndex((elem) => elem === child);
        this.parent.splice(index, 1);
      }
    }
  }
}
</script>

<template>
  <li :class="[model === state.selectedFile ? (customStyles?.selectedFileClasses ?? '') : '']" class="flex flex-col pl-3 py-2 w-full">
    <div @click="selectNode(model)" class="group flex w-full items-center justify-between cursor-pointer">
      <div class="flex items-center ">
        <svg v-if="isFolder" v-show="!isOpen || !hasChildren" class="mr-1 h-5 w-5" style="fill: none !important;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
        </svg>
        <svg v-if="isFolder && hasChildren" v-show="isOpen" class="mr-1 h-5 w-5" style="fill: none !important;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
        </svg>
        <svg v-if="!isFolder" class="mr-1 h-5 w-5" style="fill: none !important;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>
        <span v-show="!isBeingEdited" class="hover:text-white">{{ model.name }}</span>
        <input @keyup.enter="saveName" v-show="isBeingEdited" class="bg-gray-700 p-1 rounded-sm" v-model="model.name"/>
        <span class="ml-2" v-if="isFolder && hasChildren">[{{ isOpen ? '-' : '+' }}]</span>
      </div>
      <div v-if="showControls" v-show="!isBeingEdited" class="hidden group-hover:flex">
        <svg @click.stop="isEditing = true" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
        </svg>
        <svg @click.stop="addFolder" v-if="isFolder" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
        </svg>

        <svg @click.stop="addFile" v-if="isFolder" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500" style="fill: none !important;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>

        <svg @click.stop="deleteChild(model)" class="mr-2 h-5 w-5 cursor-pointer hover:text-pink-500 fill-none" style="fill: none !important;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
        </svg>

      </div>
    </div>

    <div v-show="isOpen" v-if="isFolder" class="ml-3">
      <ul :class="{'mt-1': hasChildren}" class="w-full text-gray-300 ">
        <tree-item v-for="child in sortedChildren"
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