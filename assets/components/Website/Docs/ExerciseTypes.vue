<script setup>
    import Table from './Table.vue';
    import DocCode from './Code.vue';

    const props = defineProps({
        type: String,
        interface: String,
        methods: String
    })
</script>

<template>
    <Table>
        <tr>
            <td class="whitespace-nowrap py-2 pr-3 text-xs font-medium text-gray-900 sm:pl-0">Type</td>
            <td class="whitespace-nowrap py-2 pr-3 text-xs font-medium text-gray-900 sm:pl-0"><doc-code>{{ type }}</doc-code></td>
        </tr>
        <tr>
            <td class="whitespace-nowrap py-2 pr-3 text-xs font-medium text-gray-900 sm:pl-0">Interface</td>
            <td class="whitespace-nowrap py-2 pr-3 text-xs font-medium text-gray-900 sm:pl-0"><doc-code>{{ interface }}</doc-code></td>
        </tr>
        <tr>
            <td class="whitespace-nowrap py-2 pr-3 text-xs font-medium text-gray-900 sm:pl-0">Methods to implement</td>
            <td class="whitespace-nowrap py-2 pr-3 text-xs font-medium text-gray-900 sm:pl-0"><doc-code>{{ methods }}</doc-code></td>
        </tr>
    </Table>
</template>