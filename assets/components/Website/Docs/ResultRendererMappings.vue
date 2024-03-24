<script setup>
import DocCode from "./DocCode.vue";
import List from "./DocList.vue";
import ListItem from "./DocListItem.vue";

defineProps({
  results: Array,
});
</script>

<template>
  <List title="Default Mappings:">
    <ListItem v-for="result in results" :key="result[0]">
      <div class="">
        <h4 class="mb-2 font-mono font-semibold">{{ result[0] }}</h4>
        <doc-code class="">{{ result[1] }}</doc-code>
      </div>
    </ListItem>
  </List>
</template>
