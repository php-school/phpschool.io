import { ref } from "vue";

export const editor = ref({
  files: {},
  addFile(fileName, fileContent) {
    this.files[fileName] = fileContent;
  },
  setFileContent(fileName, fileContent) {
    this.files[fileName] = fileContent;
  },
  getFileContent(fileName) {
    return this.files[fileName];
  },
});
