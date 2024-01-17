<script setup>
import { useEventStore } from '../../../stores/events.js'
const events = useEventStore()

import Heading from '../PageHeading.vue'
</script>

<template>
    <div>
        <section class="bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed items-stretch">
            <Heading>
                <template v-slot:title> Events </template>

                <template v-slot:description>
                    Past and present PHP School meet-ups, see you soon!
                </template>
            </Heading>
        </section>
        <div class="w-full bg-gray-900">
            <div class="flex flex-col mx-auto max-w-3xl text-left text-gray-800 p-8">
                <div v-if="events.events.length > 0" class="w-full pb-6">
                    <h2 class="px-8 text-2xl font-mono text-gray-300">Upcoming Events</h2>

                    <ul v-if="events.events.length > 0" class="w-full pb-6">
                        <li
                            class="p-8 border-b last:border-none border-gray-600"
                            v-for="event in events.events"
                            :key="event.id"
                        >
                            <h3 class="text-lg text-gray-300 pb-4 font-mono">
                                {{ event.date_formatted }}
                            </h3>
                            <h2 class="text-2xl pb-6 text-[#e91e63] font-mono font-bold">
                                {{ event.name }}
                            </h2>

                            <a
                                v-if="event.poster"
                                target="_blank"
                                class=""
                                :href="'/uploads/' + event.poster"
                            >
                                <img :src="'/uploads/' + event.poster" :alt="event.name" />
                            </a>

                            <p class="text-sm text-gray-300 whitespace-pre-line">
                                {{ event.description }}
                            </p>
                            <p v-if="event.link" class="py-4 text-gray-300">
                                More info:
                                <a
                                    target="_blank"
                                    class="text-[#e91e63] hover:underline"
                                    :href="event.link"
                                    >{{ event.link }}</a
                                >
                            </p>

                            <p class="">
                                <a
                                    target="_blank"
                                    class="text-[#e91e63] hover:underline"
                                    :href="
                                        'https://maps.google.com/?q=' +
                                        encodeURIComponent(event.venueLines.join(','))
                                    "
                                >
                                    <address class="text-sm">
                                        {{ event.venueLines.join(', ') }}
                                    </address>
                                </a>
                            </p>
                        </li>
                    </ul>
                </div>
                <h2 v-else class="px-8 text-2xl text-gray-300 font-mono pb-6">
                    No upcoming events
                </h2>

                <hr class="border-pink-600/40" />

                <ul v-if="events.previousEvents.length > 0" class="w-full pt-6">
                    <h2 class="px-8 text-2xl font-mono text-gray-300">Previous Events</h2>

                    <li
                        class="p-8 border-b last:border-none border-gray-600"
                        v-for="event in events.previousEvents"
                        :key="event.id"
                    >
                        <h3 class="text-lg text-gray-300 pb-4 font-mono">
                            {{ event.date_formatted }}
                        </h3>
                        <h2 class="text-2xl pb-6 text-[#e91e63] font-mono font-bold">
                            {{ event.name }}
                        </h2>

                        <a
                            v-if="event.poster"
                            target="_blank"
                            :href="'https://www.phpschool.io/uploads/' + event.poster"
                        >
                            <img :src="'/uploads/' + event.poster" :alt="event.name" />
                        </a>

                        <p class="text-sm text-gray-300 whitespace-pre-line">
                            {{ event.description }}
                        </p>
                        <p v-if="event.link" class="py-4 text-gray-300">
                            More info:
                            <a
                                target="_blank"
                                class="text-[#e91e63] hover:underline"
                                :href="event.link"
                                >{{ event.link }}</a
                            >
                        </p>

                        <p class="">
                            <a
                                target="_blank"
                                class="text-[#e91e63] hover:underline"
                                :href="
                                    'https://maps.google.com/?q=' +
                                    encodeURIComponent(event.venueLines.join(','))
                                "
                            >
                                <address class="text-sm">
                                    {{ event.venueLines.join(', ') }}
                                </address>
                            </a>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
