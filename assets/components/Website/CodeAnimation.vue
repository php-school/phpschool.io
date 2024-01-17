<template>
    <div class="container">
        <div class="w-100 h-100 text-white text-xs font-orbitron overflow-hidden">
            <div class="flex flex-col">
                <div class="">
                    <div class="h-[20px]" v-for="(line, index) in displayedCode" :key="index">
                        {{ line }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const displayedCode = ref([])

const codeSnippets = [
    "learnToCode('PHP');",
    "<?php echo 'Hello, world!'; ?>",
    '$variable = 42;',
    'function add($a, $b) { return $a + $b; }',
    '$array = [1, 2, 3, 4, 5];',
    'class MyClass { $name $skill; }',
    '// This is a PHP comment',
    "<?php $programmingLanguages = ['PHP', 'Javascript']",
    "$favoriteLanguage = 'PHP'",
    "$developer = new Programmer('You', 'expert')"
]

onMounted(() => {
    for (let i = 0; i < 20; i++) {
        displayedCode.value.push(getRandomCodeSnippet())
    }
})

const getRandomCodeSnippet = () => {
    return codeSnippets[Math.floor(Math.random() * codeSnippets.length)]
}

setInterval(() => {
    displayedCode.value.shift()
    displayedCode.value.push(getRandomCodeSnippet())
}, 400)
</script>
