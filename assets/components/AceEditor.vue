<script>

import ace from 'ace-builds';
import 'ace-builds/src-noconflict/theme-monokai';
import 'ace-builds/src-noconflict/mode-php';
import { markRaw} from "vue";
import {editor} from "./stores/editor.js";

export default {
  data() {
    return {
      editor
    }
  },
  props: {
    readonly: {
      default: false,
      type: Boolean
    },
    minLines: Number,
    maxLines: Number,
  },
  mounted() {
    const options = {};
    if (this.minLines) {
      options.minLines = this.minLines;
    }

    if (this.maxLines) {
      options.maxLines = this.maxLines;
    }

    this._editor = markRaw(ace.edit(this.$el, options));

    this._editor.setOption('useWorker', false);
    this._editor.setTheme("ace/theme/monokai");
    this._editor.session.setMode("ace/mode/php");
    this._editor.setValue(this.editor.getFileContent('solution.php'));

    if (this.readonly) {
      this._editor.setReadOnly(true);
    }

    this._editor.session.on('change', this.change);

  },
  methods: {
      change() {
        const content = this._editor.session.getValue();
        this.editor.setFileContent('solution.php', content);
        this.$emit('change', content)
      }
  },
  beforeUnmount() {
    this._editor.destroy();
  },
}
</script>

<template>
  <div class="h-full border-0"></div>
</template>