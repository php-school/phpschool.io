<script>

import ace from 'ace-builds';
import 'ace-builds/src-noconflict/theme-monokai';
import 'ace-builds/src-noconflict/mode-php';
import { markRaw} from "vue";

export default {
  props: {
    readonly: {
      default: false,
      type: Boolean
    },
    file: Object,
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
    this._editor.setValue(this.file.content, 1);

    if (this.readonly) {
      this._editor.setReadOnly(true);
    } else {
      this._editor.session.on('change', this.change);
    }
  },
  methods: {
      change() {
        const content = this._editor.session.getValue();
        this.file.content = content;
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