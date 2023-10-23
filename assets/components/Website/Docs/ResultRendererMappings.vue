<script setup>
import Table from './Table.vue';
import DocCode from './Code.vue';

const props = defineProps({
    results: Array
})
</script>

<template>
    <Table>
        <tr v-for="result in results">
            <td class="whitespace-nowrap py-2 pr-3 text-xs font-medium text-gray-900 sm:pl-0"><doc-code>{{ result[0] }}</doc-code></td>
            <td class="whitespace-nowrap py-2 pr-3 text-xs font-medium text-gray-900 sm:pl-0"><doc-code>{{ result[1] }}</doc-code></td>
        </tr>
    </Table>
</template>