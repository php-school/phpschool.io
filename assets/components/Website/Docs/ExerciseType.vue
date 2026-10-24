<script setup>
import Table from "./DocTable.vue";
import DocCode from "./DocCode.vue";

defineProps({
  type: String,
  interfaceToImplement: String,
  methods: String,
});
</script>

<template>
  <Table>
    <tr>
      <td class="py-2 pr-3 text-xs font-medium whitespace-nowrap text-white sm:pl-0">Type</td>
      <td class="py-2 pr-3 text-xs font-medium whitespace-nowrap text-white sm:pl-0">
        <doc-code>{{ type }}</doc-code>
      </td>
    </tr>
    <tr>
      <td class="py-2 pr-3 text-xs font-medium whitespace-nowrap text-white sm:pl-0">Interface</td>
      <td class="py-2 pr-3 text-xs font-medium whitespace-nowrap text-white sm:pl-0">
        <doc-code>{{ interfaceToImplement }}</doc-code>
      </td>
    </tr>
    <tr>
      <td class="py-2 pr-3 text-xs font-medium whitespace-nowrap text-white sm:pl-0">Methods to implement</td>
      <td class="py-2 pr-3 text-xs font-medium whitespace-nowrap text-white sm:pl-0">
        <doc-code>{{ methods }}</doc-code>
      </td>
    </tr>
  </Table>
</template>
