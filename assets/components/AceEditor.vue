<script>

import ace from 'ace-builds';
import { markRaw} from "vue";

export default {
  props: {
    file_path: String,
    file_content: String,
    readonly: {
      default: false,
      type: Boolean
    },
  },
  mounted() {
    this._editor = markRaw(ace.edit(this.$el, {
      minLines: 20,
      maxLines: 20
    }));
    this._editor.setTheme("ace/theme/monokai");
    this._editor.session.setMode("ace/mode/php");
    this._editor.setValue(this.file_content);

    if (this.readonly) {
      this._editor.setReadOnly(true);
    }
  },
  beforeUnmount() {
    this._editor.destroy();
  },
}
</script>

<template>
  <div></div>
</template>