<script>

import Modal from "../Modal.vue";
import {ExclamationTriangleIcon, CheckIcon, XMarkIcon} from '@heroicons/vue/24/solid'
import FunctionRequirementsFailure from "./FunctionRequirementsFailure.vue";
import CliRun from "./CliRun.vue";
import CgiRun from "./CgiRun.vue";
import Failure from "./Failure.vue";

export default {
  components: {
    CliRun,
    CgiRun,
    FunctionRequirementsFailure,
    Failure,
    Modal,
    ExclamationTriangleIcon,
    CheckIcon,
    XMarkIcon
  },
  props: {
    results: Array
  },
  computed: {
    successes() {
      return this.results.filter(r => r.success === true);
    },
    failures() {
      return this.results.filter(r => r.success === false);
    }
  },
  data() {
    return {
      typesToComponents:  {
        'FunctionRequirementsFailure': 'FunctionRequirementsFailure',
        'PhpSchool\\PhpWorkshop\\Result\\Cli\\CliResult': 'CliRun',
        'PhpSchool\\PhpWorkshop\\Result\\Cgi\\CgiResult': 'CgiRun',
        'PhpSchool\\PhpWorkshop\\Result\\Failure': 'Failure',
        'PhpSchool\\PhpWorkshop\\Result\\FunctionRequirementsFailure': 'FunctionRequirementsFailure'
      },
    }
  },
}
</script>

<template>
  <ul v-show="results" id="results" class="my-8 space-y-4 text-left text-gray-100 max-h-full h-max overflow-y-scroll">
    <li v-for="success in successes" class="flex items-start space-x-3">
      <CheckIcon fill="currentColor" class="flex-shrink-0 w-5 h-5 text-green-500"/>
      <div class="flex flex-col w-full">
        <p>{{ success.name }} </p>
      </div>
    </li>

    <li v-for="failure in failures" class="flex items-start space-x-3">
      <XMarkIcon fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500"/>
      <div class="flex flex-col w-full">
        {{failure.name }}
        <component v-if="typesToComponents.hasOwnProperty(failure.type)" :is="typesToComponents[failure.type]" :data="failure"></component>
      </div>
    </li>
  </ul>
</template>