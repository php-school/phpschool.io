<script>
import { ref, provide } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/solid'


export default {
  components: {
    XMarkIcon
  },
  setup(props, { slots }) {
    const tabTitles = ref(slots.default().map(tab => {
      return tab.props.title;
    }));

    const selectedTitle = ref(tabTitles.value[0]);

    provide('selectedTitle', selectedTitle);

    return {
      tabTitles,
      selectedTitle,
    }
  },
  methods: {
    closeTab(tab) {
      console.log("close tab")
    }
  }
}
</script>

<template>
  <div class="tabs">
    <ul class="tabs list-reset flex justify-start mb-1">
      <li v-for="title in tabTitles" :key="title" :class="{ 'border-pink-500' : title === selectedTitle }"
          @click="selectedTitle = title"
          class="flex border-solid border-t-2 mr-1 bg-stone-700 inline-block py-2 px-4 text-white hover:text-pink-500 text-xs no-underline items-center">
        <a href="#" class="">
          {{ title }}
        </a>
        <XMarkIcon @click="closeTab(tab)" class="cursor-pointer ml-2 w-3 h-3 text-zinc-400"  />
      </li>
    </ul>
    <slot/>
  </div>
</template>