<script>
import { ref, provide } from 'vue'

export default {
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
        <svg v-show="false" @click="closeTab(tab)" class="cursor-pointer ml-2" width="12" height="12"
             viewBox="0 0 24 24" data-v-cbb0a4e5="">
          <line stroke="#999" x1="18" y1="6" x2="6" y2="18" data-v-cbb0a4e5=""></line>
          <line stroke="#999" x1="6" y1="6" x2="18" y2="18" data-v-cbb0a4e5=""></line>
        </svg>
      </li>
    </ul>
    <slot/>
  </div>
</template>