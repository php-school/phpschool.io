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
    value: {
      type: String,
      required: true,
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

    this._editor.setValue(this.value, 1);
    this._contentBackup = this.value;
    this._isSettingContent = false;

    if (this.readonly) {
      this._editor.setReadOnly(true);
    } else {
      this._editor.session.on('change', this.change);
    }
  },
  methods: {
      change() {
        // ref: https://github.com/CarterLi/vue3-ace-editor/issues/11
        if (this._isSettingContent) {
          return;
        }
        const content = this._editor.session.getValue();
        this._contentBackup = content;
        this.$emit('update:value', content);
      },
  },
  emits: ['update:value'],
  watch: {
    value(val) {
      if (this._contentBackup !== val) {
        try {
          this._isSettingContent = true;
          this._editor.setValue(val, 1);
        } finally {
          this._isSettingContent = false;
        }
        this._contentBackup = val;
      }
    },
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
  @apply !bg-gradient-to-b to-30% !from-gray-900 !to-[#0c1220];
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