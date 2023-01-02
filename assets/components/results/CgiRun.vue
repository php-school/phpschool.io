<script>

import Modal from "../Modal.vue";
import {ExclamationTriangleIcon, Cog6ToothIcon} from '@heroicons/vue/24/solid'
import CgiOutputMismatch from "./CgiOutputMismatch.vue";
import Failure from "./Failure.vue";

export default {
  components: {
    Modal,
    Cog6ToothIcon,
    CgiOutputMismatch,
    Failure
  },
  props: {
    data: Object
  },
  computed: {
    resultCount() {
      return this.data.results.length;
    },
    failures() {
      return this.data.results.filter(r => r.success === false);
    }
  },
  data() {
    return {
      typesToComponents:  {
        'PhpSchool\\PhpWorkshop\\Result\\Cgi\\RequestFailure': 'CgiOutputMismatch',
        'PhpSchool\\PhpWorkshop\\Result\\Cgi\\GenericFailure': 'Failure',
      },
      openModal: false,
      currentFailure: null
    }
  },
  methods: {
    openInfoModal(failure) {
      this.currentFailure = failure
      this.openModal = true;
    },
    haveHeaders(headers) {
      return Object.keys(headers).length > 0;
    }
  }
}
</script>

<template>
  <div class="mt-3 pl-2 border-red-500 border-l-2 border-solid" v-for="(failure, i) in failures">
    <div class="w-full flex justify-between">
      <span class="w-2/3 inline text-gray-300 italic">Run #{{i + 1}}</span>
      <button class="ml-2 underline text-[#E91E63] text-left p-x2" @click="openInfoModal(failure)">Run info</button>
    </div>
    <component v-if="typesToComponents.hasOwnProperty(failure.type)" :is="typesToComponents[failure.type]" :data="failure"></component>
  </div>

  <Transition enter-active-class="transition-opacity duration-100 ease-in" leave-active-class="transition-opacity duration-200 ease-in" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
    <Modal :scroll-content="true" size="2xl" max-height="max-h-[calc(5/6*100%)]" v-if="openModal" @close="openModal = false">
      <template #header>
        <div class="flex flex-col">
          <div class="flex items-center ">
            <Cog6ToothIcon class="h-6 w-6 text-pink-500 mr-2"/>

            <h2 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">Run context</h2>
          </div>
        </div>
      </template>

      <template #body class="">
        <div id="cgi-run-info">
          <div class="mb-8">
            <h2 class="mb-2 text-lg pt-0">Request details</h2>
            <span class="text-base font-bold text-violet-400">{{currentFailure.request.method}}</span>
            <span class="ml-2 text-base text-gray-300">{{currentFailure.request.uri}}</span>
          </div>

          <div v-if="haveHeaders(currentFailure.request.headers)" class="mb-8">
            <h2 class="mb-2 text-lg pt-0">Request headers</h2>
            <code class="block mb-2 last:mb-0" v-for="(value, header) in currentFailure.request.headers">{{header}}: {{value.join(', ')}}</code>
          </div>

          <div v-if="currentFailure.request.body">
            <h2 class="mb-2 text-lg pt-0">Request body</h2>
            <pre><code class="language-sh hljs bash">{{currentFailure.request.body}}</code></pre>
          </div>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end">
          <button @click="openModal = false" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
            Close
          </button>
        </div>
      </template>
    </Modal>
  </Transition>
</template>

<style>
#cgi-run-info pre {
  background-color: initial;
  @apply mb-4 p-0 border-none rounded-none;
}
#cgi-run-info pre code {
  background-color: #2a2c2d !important;
  @apply p-4 rounded-lg;
}

#cgi-run-info :not(pre)>code {
  font-size: 90%;
  background-color: #2a2c2d;
  @apply px-2 py-1 rounded;
  color: #ff75b5;
}

</style>