<script>

import ace from 'ace-builds';
import 'ace-builds/src-noconflict/theme-monokai';
import 'ace-builds/src-noconflict/mode-php';
import 'ace-builds/src-noconflict/ext-language_tools';
import 'ace-builds/src-noconflict/snippets/php';
import {markRaw} from "vue";

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

    ace.require("ace/ext/language_tools");
    this._editor = markRaw(ace.edit(this.$el, options));

    this._editor.setOption('useWorker', false);
    this._editor.setShowPrintMargin(false);
    this._editor.setTheme("ace/theme/monokai");
    this._editor.container.style.lineHeight = 2
    this._editor.session.setMode("ace/mode/php");

    this._editor.setOptions({
      enableBasicAutocompletion: true,
      enableSnippets: true,
      enableLiveAutocompletion: false
    });

    this._editor.setValue(this.file.content, 1);

    if (this.readonly) {
      this._editor.setReadOnly(true);
    } else {
      this._editor.session.on('change', this.change);
    }
  },
  methods: {
      change() {
        this.file.content = this._editor.session.getValue();
        this.$emit('changeContent', this.file);
      }
  },
  beforeUnmount() {
    this._editor.destroy();
  },
}
</script>

<template>
  <div class="h-full border-0 bg-gray-900"></div>
</template>

<style>
.ace_gutter {
  background-color: #0c1220 !important;
}

.ace_marker-layer .ace_selection {
  @apply !bg-gray-800;
}

.ace_gutter-active-line {
  @apply !bg-gray-900;
}

.ace_active-line {
  background-color: #0c1220 !important;
}

</style>