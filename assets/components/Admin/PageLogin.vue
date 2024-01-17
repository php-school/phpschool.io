<script setup>
import { ref } from 'vue'
import { useAdminStore } from '../../stores/admin'
import { ArrowPathIcon } from '@heroicons/vue/24/solid'

const adminStore = useAdminStore()

const loading = ref(false)
const errors = ref([])

const email = ref(null)
const password = ref(null)

const login = async () => {
    errors.value = {} //reset errors
    loading.value = true

    const loginResult = await adminStore.login(email.value, password.value)
    loading.value = false

    if (loginResult.success === false) {
        errors.value = loginResult.errors
    }
}
</script>
<template>
    <div class="content h-screen bg-black mx-auto flex flex-col justify-center items-center">
        <pre class="blink font-normal text-[13px] hidden md:block text-[#e91e63] mb-8">
 ____    __  __  ____        ____           __                     ___
/\  _`\ /\ \/\ \/\  _`\     /\  _`\        /\ \                   /\_ \
\ \ \L\ \ \ \_\ \ \ \L\ \   \ \ \L\_\   ___\ \ \___    ___    __  \//\ \
 \ \ ,__/\ \  _  \ \ ,__/    \/_\__ \ /\'___\ \  _`\  / __`\ / __`\ \ \ \
  \ \ \/  \ \ \ \ \ \ \/       /\ \L\ \/\ \__/\ \ \ \ \/\ \L\ /\ \L\ \_\ \_
   \ \_\   \ \_\ \_\ \_\       \ `\____\ \____\\ \_\ \_\ \____\ \____/\____\
    \/_/    \/_/\/_/\/_/        \/_____/\/____/ \/_/\/_/\/___/ \/___/\/____/
                </pre
        >
        <form
            @submit.prevent="login"
            class="w-[300px] flex flex-col justify-center items-center gap-y-4"
            action="/login"
            method="POST"
        >
            <input
                v-model="email"
                type="text"
                name="email"
                class="block w-full rounded-full border-0 px-4 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                placeholder="Email"
                required=""
            />
            <ul v-if="errors.email">
                <li v-for="err in errors.email" :key="err" class="mt-2 text-xs text-red-600">
                    {{ err }}
                </li>
            </ul>
            <input
                v-model="password"
                type="password"
                name="password"
                class="block w-full rounded-full border-0 px-4 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                placeholder="Password"
                required=""
            />
            <ul v-if="errors.password">
                <li v-for="err in errors.password" :key="err" class="mt-2 text-xs text-red-600">
                    {{ err }}
                </li>
            </ul>
            <button
                class="w-48 flex justify-center items-center text-white text-base px-10 py-2 uppercase rounded-full bg-pink-600 hover:bg-pink-500"
                type="submit"
            >
                <ArrowPathIcon v-show="loading" class="w-5 h-5 animate-spin" />
                <span v-show="!loading">Login</span>
            </button>

            <ul v-if="errors" class="text-xs text-center text-[#cc1717] my-3 p-3">
                <li v-for="err in errors.auth" :key="err" class="mb-3 last:mb-0">{{ err }}</li>
            </ul>
        </form>
    </div>
</template>
