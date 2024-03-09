<script setup>
import PageSection from "../../PageSection.vue";
import {CommandLineIcon, GlobeEuropeAfricaIcon, CheckIcon} from '@heroicons/vue/24/outline'
import PrimaryButton from '../../../PrimaryButton.vue'

const online = [
    'No setup necessary',
    'Modern and intuitive UI',
    'Save your progress across computers',
    'No editor required, just a browser',
];

const offline = [
    'Workshops are always available',
    'No internet connection required',
    'Easier to hack on exercises'
];
</script>

<template>
  <PageSection>
    <template #title>Getting Started</template>
    <div class="flex flex-col bg-gray-900 p-4 text-white mb-8">
      <p class="mb-4 max-w-4xl">
        PHP School workshops are a set of exercises focused on a specific topic or overarching theme, such as new language versions or tools and techniques. See the
        <a class="text-[#e91e63] hover:underline" href="#the-workshops">below section</a>
        for the list of available workshops.
      </p>

      <p class="mb-4 max-w-4xl">
        Workshops can be run in your browser or you can download them and run them on your own computer. Running them on your own computer is a little bit more involved, however, it is also more
        flexible and self contained.
      </p>

      <div class="grid grid-cols-1 lg:grid-cols-2 mt-8 gap-x-8">
          <div class="px-8 pt-8 max-w-2xl order-1">
              <h4 class="mb-8 mt-0 p-0 font-work-sans text-4xl font-bold capitalize not-italic text-white flex"><GlobeEuropeAfricaIcon class="text-pink-600 h-10 w-10 mr-2"></GlobeEuropeAfricaIcon> Online Browser Based</h4>
              <p class="mb-4">Simply login or register with your GitHub account, head to the dashboard, select an exercise and go.</p>
              <p class="mb-4">Not all exercises are available in the online IDE, for example if some workshops are not kept up to date they may get dropped. However, you will always be able to install them on your own computer.</p>
          </div>
          <div class="px-8 pt-8 max-w-2xl order-4 lg:order-2">
              <h4 class="mb-8 mt-0 p-0 font-work-sans text-4xl font-bold capitalize not-italic text-white flex"><CommandLineIcon class="text-pink-600 h-10 w-10 mr-2"></CommandLineIcon> Offline, in your Terminal</h4>
              <p class="mb-4">
                  You need to configure your system, install the Workshop Manager, and then install the workshops. Head on over to the
                  <a class="text-[#e91e63] hover:underline" href="/offline">Running PHP School locally page to get the low down.</a>

              </p>
              <p class="mb-4">Naturally, you need to be comfortable in a terminal such as iTerm and know your way around a decent text editor or IDE.</p>
          </div>
          <ul class="mt-4 px-8 pt-8 order-2 lg:order-3">
              <li v-for="line in online" :key="line" class="group">
                  <div class="py-3 mr-16 pl-1 text-white">
                      <div class="flex items-start text-base text-pink-600">
                          <CheckIcon class="ml-1 mr-2 mt-0.5 h-5 w-5 flex-none" />
                          <span class="">{{line}}</span>
                      </div>
                  </div>
              </li>
          </ul>
          <ul class="mt-4 px-8 pt-8 order-5 lg:order-4">
              <li v-for="line in offline" :key="line" class="group">
                  <div class="py-3 mr-16 pl-1 text-white">
                      <div class="flex items-start text-base text-pink-600">
                          <CheckIcon class="ml-1 mr-2 mt-0.5 h-5 w-5 flex-none " />
                          <span class="">{{line}}</span>
                      </div>
                  </div>
              </li>
          </ul>
          <div class="p-8 order-3 lg:order-5">
              <PrimaryButton to="/online">To the workshops</PrimaryButton>
          </div>
          <div class="p-8 order-6">
              <PrimaryButton to="/online">To the installation instructions</PrimaryButton>
          </div>
      </div>
    </div>
  </PageSection>
</template>
