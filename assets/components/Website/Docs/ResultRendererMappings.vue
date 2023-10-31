<script setup>
import Table from './Table.vue';
import DocCode from './Code.vue';
import List from "./List.vue";
import ListItem from "./ListItem.vue";

const props = defineProps({
    results: Array
})
</script>

<template>
    <List title="Default Mappings:">
        <ListItem v-for="result in results">
            <div class="">
                <h4 class="font-mono font-semibold mb-2">{{ result[0] }}</h4>
                <doc-code class="">{{ result[1] }}</doc-code>
            </div>
        </ListItem>
    </List>
</template>