<script setup>
import { ChevronRightIcon } from '@heroicons/vue/24/solid'

defineProps({
    exercise: Object,
    run: Object
})

const longestRunCliArg = (args) => {
    return Math.max(...args.map((r) => r.length))
}

const haveHeaders = (headers) => {
    return Object.keys(headers).length > 0
}

const headers = (headers) => {
    return Object.entries(headers).reduce((carry, [header, value]) => {
        return carry + header + ': ' + value.join(', ') + '\n'
    }, '')
}
</script>

<template>
    <div id="run-info" class="w-full">
        <div v-if="exercise.type === 'CLI'">
            <h2 class="font-mono mb-2 text-[#E91E63] text-lg pt-0">Command line arguments</h2>
            <div v-if="run.args.length">
                <div class="mb-8">
                    <ul
                        v-if="longestRunCliArg(run.args) > 5"
                        class="ml-2 mt-2 list-disc list-inside"
                    >
                        <li v-for="arg in run.args" :key="arg" class="list-item p-1 text-white">
                            {{ arg }}
                        </li>
                    </ul>
                    <div v-else>
                        <code v-for="arg in run.args" :key="arg" class="px-2 mr-2"> {{ arg }}</code>
                    </div>
                </div>
            </div>

            <p v-else class="truncate font-medium text-white">
                <span class="text-white text-sm"
                    >Your program was executed with no command line arguments.</span
                >
            </p>
        </div>

        <div v-if="exercise.type === 'CGI'">
            <div class="mb-8">
                <h2 class="font-mono mb-2 text-[#E91E63] text-lg pt-0">Request details</h2>
                <span class="text-base font-bold text-violet-400">{{ run.request.method }}</span>
                <span class="ml-2 text-base text-gray-300">{{ run.request.uri }}</span>
            </div>

            <div v-if="haveHeaders(run.request.headers)" class="mb-8">
                <h2 class="font-mono mb-2 text-[#E91E63] text-lg pt-0">Request headers</h2>
                <pre><code class="block mb-2 last:mb-0 text-[#ff75b5]">{{headers(run.request.headers)}}</code></pre>
            </div>

            <div v-if="run.request.body">
                <h2 class="font-mono mb-2 text-[#E91E63] text-lg pt-0">Request body</h2>
                <pre><code class="language-sh hljs bash">{{run.request.body}}</code></pre>
            </div>
        </div>

        <div class="mt-4">
            <h2 class="font-mono text-[#E91E63] mb-2 text-lg pt-0">Output</h2>

            <div class="mt-2" v-if="exercise.type === 'CLI' && run.output">
                <pre><code class="language-shell hljs shell"><div class="flex items-center mb-2"><ChevronRightIcon class="flex-none text-pink-500 w-auto h-5 mr-1 -ml-1"/>php solution.php {{run.args.join(' ')}}</div>{{run.output}}</code></pre>
            </div>
            <div class="mt-2" v-if="exercise.type === 'CGI' && run.output">
                <pre><code class="language-shell hljs shell">{{run.output}}</code></pre>
            </div>
            <div v-if="!run.output" class="" role="alert">
                <span class="sr-only">Info</span>
                <div class="text-white">Your program produced no output.</div>
            </div>
        </div>
    </div>
</template>

<style>
#run-info pre {
    background-color: initial;
    @apply mb-4 p-0 border-none rounded-none;
}
#run-info pre code {
    background-color: #2a2c2d !important;
    @apply p-4 rounded-lg block text-sm;
}

#run-info :not(pre) > code {
    font-size: 90%;
    background-color: #2a2c2d;
    @apply px-2 py-1 rounded;
    color: #ff75b5;
}
</style>
