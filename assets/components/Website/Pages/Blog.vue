<script setup>


import {onMounted, ref} from "vue";

const events = ref([]);
const previousEvents = ref([]);

onMounted(() => {
    fetch('/api/events').then(res => res.json()).then(data => {
        events.value = data.events;
        previousEvents.value = data.previousEvents;
    })
});
</script>

<template>
    <div class="flex flex-col mx-auto max-w-3xl text-left text-gray-800 p-8">

        <ul v-if="events.length > 0" class="w-full pb-6" >
            <h2 class="px-8 text-2xl font-mono">Upcoming Events</h2>

            <li class="p-8 border-b border-[#e91e63] border-dashed " v-for="event in events">
                <h3 class="text-lg pb-4 font-mono">{{event.date}}</h3>
                <h2 class="text-2xl pb-6 text-[#e91e63] font-mono font-bold"> {{event.name}}</h2>

                <a v-if="event.poster" target="_blank" class="" :href="'/uploads/' + event.poster">
                    <img :src="'https://www.phpschool.io/uploads/' + event.poster" :alt="event.name">
                </a>

                <p class="text-sm whitespace-pre-line ">{{event.description}}</p>
                <p v-if="event.link" class="py-4 ">More info: <a target="_blank" class="text-[#e91e63] hover:underline" :href="event.link">{{ event.link }}</a></p>

                <p class="">
                    <a target="_blank" class="text-[#e91e63] hover:underline" :href="'https://maps.google.com/?q=' + encodeURIComponent(event.venueLines.join(','))">
                        <address class="text-sm">
                            {{event.venueLines.join(', ')}}
                        </address>
                    </a>
                </p>
            </li>
        </ul>

        <h2 v-else class="px-8 text-2xl font-mono pb-6">No upcoming events</h2>

        <hr class="border-pink-600/40">

        <ul v-if="previousEvents.length > 0" class="w-full pt-6">
           <h2 class="px-8 text-2xl font-mono">Previous Events</h2>

           <li class="p-8 border-b last:border-none" v-for="event in previousEvents">
               <h3 class="text-lg pb-4 font-mono">{{event.date}}</h3>
               <h2 class="text-2xl pb-6 text-[#e91e63] font-mono font-bold"> {{event.name}}</h2>

               <a v-if="event.poster" target="_blank" :href="'https://www.phpschool.io/uploads/' + event.poster">
                   <img :src="'https://www.phpschool.io/uploads/' + event.poster" :alt="event.name">
               </a>

               <p class="text-sm whitespace-pre-line ">{{event.description}}</p>
               <p v-if="event.link" class="py-4 ">More info: <a target="_blank" class="text-[#e91e63] hover:underline" :href="event.link">{{ event.link }}</a></p>

               <p class="">
                   <a target="_blank" class="text-[#e91e63] hover:underline" :href="'https://maps.google.com/?q=' + encodeURIComponent(event.venueLines.join(','))">
                       <address class="text-sm">
                           {{event.venueLines.join(', ')}}
                       </address>
                   </a>
               </p>
           </li>
       </ul>
    </div>
</template>