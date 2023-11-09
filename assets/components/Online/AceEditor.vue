<script setup>

import ace from 'ace-builds';
import 'ace-builds/src-noconflict/theme-monokai';
import 'ace-builds/src-noconflict/mode-php';
import 'ace-builds/src-noconflict/ext-language_tools';
import 'ace-builds/src-noconflict/snippets/php';
import {markRaw, onBeforeUnmount, onMounted, ref, watch} from "vue";

const props = defineProps({
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
});

const emit = defineEmits(['update:value']);

let editor = null;
const isSettingContent = ref(false);
const contentBackup = ref(null);

const container = ref(null);

const change = () => {
  // ref: https://github.com/CarterLi/vue3-ace-editor/issues/11
  if (isSettingContent.value) {
    return;
  }
  const content = editor.session.getValue();
  contentBackup.value = content;
  emit('update:value', content);
}

onMounted(() => {
  const options = {};
  if (props.minLines) {
    options.minLines = props.minLines;
  }

  if (props.maxLines) {
    options.maxLines = props.maxLines;
  }

  ace.require("ace/ext/language_tools");
  editor = markRaw(ace.edit(container.value, options));

  editor.setOption('useWorker', false);
  editor.setShowPrintMargin(false);
  editor.setTheme("ace/theme/monokai");
  editor.container.style.lineHeight = 2
  editor.session.setMode("ace/mode/php");

  editor.setOptions({
    enableBasicAutocompletion: true,
    enableSnippets: true,
    enableLiveAutocompletion: false
  });

  editor.setValue(props.value, 1);
  contentBackup.value = props.value;
  isSettingContent.value = false;

  if (props.readonly) {
    editor.setReadOnly(true);
  } else {
    editor.session.on('change', change);
  }
});

onBeforeUnmount(() => {
  if (editor) {
    editor.destroy();
  }
});

watch(() => props.value, (val) => {
  if (contentBackup.value !== val) {
    try {
      isSettingContent.value = true;
      editor.setValue(val, 1);
    } finally {
      isSettingContent.value = false;
    }
    contentBackup.value = val;
  }
});
</script>

<template>
  <div ref="container" class="h-full border-0 bg-gray-900"></div>
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