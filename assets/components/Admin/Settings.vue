<script setup>

import {ref} from "vue";
import Alert from "../Online/Alert.vue";

const props = defineProps({
    search: {
        type: String,
        default: '',
    }
});

const showCacheClearSuccess = ref(false);
const cacheClearError = ref('');
const showCacheClearError = ref(false);

const clearCache = () => {
    fetch('/admin/cache/clear', { method: 'POST' })
        .then((response) => {
            if (response.ok) {
                showCacheClearSuccess.value = true;
                return;
            }
            return Promise.reject(response);
        })
        .catch((response) => {
            response.json().then((json) => {

                if (json.error) {
                    cacheClearError.value = json.error;
                }

                showCacheClearError.value = true;
            })
        });
}
</script>

<template>
    <!-- cc alerts -->
    <alert type="error" :message="cacheClearError ?? 'An error occurred. Please try again later.'" :timeout="4000" v-if="showCacheClearError" @close="showCacheClearError = false"></alert>
    <alert type="success" message="Cache was successfully cleared" :timeout="4000" v-if="showCacheClearSuccess" @close="showCacheClearSuccess = false"></alert>

    <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
        <h1 class="text-base font-semibold leading-7 text-white">Settings</h1>
    </header>

    <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-2 lg:px-8 border-b border-pink-600/30">
        <div>
            <h2 class="text-base font-semibold leading-7 text-white">Clear cache</h2>
            <p class="mt-1 text-sm leading-6 text-gray-400">Clear the full page and Redis cache. This includes Doctrine and HTML cache.</p>
        </div>

        <div class="flex items-start justify-end">
            <button @click="clearCache" type="submit" class="rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-400">Clear cache</button>
        </div>
    </div>
</template>

