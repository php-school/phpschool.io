<script setup>
import { useEventStore } from "../../../stores/events.js";
const events = useEventStore();

import Heading from "../PageHeading.vue";
</script>

<template>
  <div>
    <section class="items-stretch bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed">
      <Heading>
        <template v-slot:title>Events</template>

        <template v-slot:description>Past and present PHP School meet-ups, see you soon!</template>
      </Heading>
    </section>
    <div class="w-full bg-gray-900">
      <div class="mx-auto flex max-w-3xl flex-col p-8 text-left text-gray-800">
        <div v-if="events.events.length > 0" class="w-full pb-6">
          <h2 class="px-8 font-mono text-2xl text-gray-300">Upcoming Events</h2>

          <ul v-if="events.events.length > 0" class="w-full pb-6">
            <li class="border-b border-gray-600 p-8 last:border-none" v-for="event in events.events" :key="event.id">
              <h3 class="pb-4 font-mono text-lg text-gray-300">
                {{ event.date_formatted }}
              </h3>
              <h2 class="pb-6 font-mono text-2xl font-bold text-[#e91e63]">
                {{ event.name }}
              </h2>

              <a v-if="event.poster" target="_blank" class="" :href="'/uploads/' + event.poster">
                <img :src="'/uploads/' + event.poster" :alt="event.name" />
              </a>

              <p class="whitespace-pre-line text-sm text-gray-300">
                {{ event.description }}
              </p>
              <p v-if="event.link" class="py-4 text-gray-300">
                More info:
                <a target="_blank" class="text-[#e91e63] hover:underline" :href="event.link">{{ event.link }}</a>
              </p>

              <p class="">
                <a
                  target="_blank"
                  class="text-[#e91e63] hover:underline"
                  :href="'https://maps.google.com/?q=' + encodeURIComponent(event.venueLines.join(','))"
                >
                  <address class="text-sm">
                    {{ event.venueLines.join(", ") }}
                  </address>
                </a>
              </p>
            </li>
          </ul>
        </div>
        <h2 v-else class="px-8 pb-6 font-mono text-2xl text-gray-300">No upcoming events</h2>

        <hr class="border-pink-600/40" />

        <ul v-if="events.previousEvents.length > 0" class="w-full pt-6">
          <h2 class="px-8 font-mono text-2xl text-gray-300">Previous Events</h2>

          <li
            class="border-b border-gray-600 p-8 last:border-none"
            v-for="event in events.previousEvents"
            :key="event.id"
          >
            <h3 class="pb-4 font-mono text-lg text-gray-300">
              {{ event.date_formatted }}
            </h3>
            <h2 class="pb-6 font-mono text-2xl font-bold text-[#e91e63]">
              {{ event.name }}
            </h2>

            <a v-if="event.poster" target="_blank" :href="'https://www.phpschool.io/uploads/' + event.poster">
              <img :src="'/uploads/' + event.poster" :alt="event.name" />
            </a>

            <p class="whitespace-pre-line text-sm text-gray-300">
              {{ event.description }}
            </p>
            <p v-if="event.link" class="py-4 text-gray-300">
              More info:
              <a target="_blank" class="text-[#e91e63] hover:underline" :href="event.link">{{ event.link }}</a>
            </p>

            <p class="">
              <a
                target="_blank"
                class="text-[#e91e63] hover:underline"
                :href="'https://maps.google.com/?q=' + encodeURIComponent(event.venueLines.join(','))"
              >
                <address class="text-sm">
                  {{ event.venueLines.join(", ") }}
                </address>
              </a>
            </p>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
