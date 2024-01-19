<script setup>
import Heading from "../PageHeading.vue";

import { docs, sectionRoute } from "../Docs/contents";
import { useRoute } from "vue-router";
import Title from "../Docs/DocTitle.vue";

const route = useRoute();

import docsearch from "@docsearch/js";
import { computed, onMounted, ref } from "vue";

const docSearchContainer = ref(null);

onMounted(() => {
  docsearch({
    container: docSearchContainer.value,
    appId: "7LDU5I0DPZ",
    indexName: "phpschool",
    apiKey: "ece29c8970bdd054ac5a6ef17ceb491e",
    placeholder: "Search...",
    translations: {
      button: { buttonText: "Search..." },
    },
  });
});

const previousSection = computed(() => {
  const section = route.meta.section;
  const group = route.meta.group;

  const index = group.sections.findIndex((s) => s.title === section.title);

  return group.sections[index - 1] ?? null;
});

const prevLink = computed(() => {
  return previousSection.value ? sectionRoute(route.meta.group, previousSection.value) : "#";
});

const nextSection = computed(() => {
  const section = route.meta.section;
  const group = route.meta.group;

  const index = group.sections.findIndex((s) => s.title === section.title);

  return group.sections[index + 1] ?? null;
});

const nextLink = computed(() => {
  return nextSection.value ? sectionRoute(route.meta.group, nextSection.value) : "#";
});

const homeSection = computed(() => {
  const group = route.meta.group;
  return group.sections[0] ?? null;
});

const homeLink = computed(() => {
  return homeSection.value ? sectionRoute(route.meta.group, homeSection.value) : "#";
});
</script>

<template>
  <div>
    <section class="items-stretch bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed">
      <Heading>
        <template v-slot:title>Documentation</template>

        <template v-slot:description>
          Ready for the next steps? Use the PHP School workshop documentation to build you own workshop and help teach
          others how to code with PHP!
        </template>
      </Heading>
    </section>
    <div class="min-h-screen w-full bg-gray-900">
      <div class="flex flex-wrap p-6 text-left font-open-sans text-gray-800">
        <div class="w-full border-r border-gray-600 bg-gray-900 pl-48 md:w-3/12 md:pt-8">
          <div ref="docSearchContainer" class="mb-2 mr-4"></div>
          <template v-for="group in docs" :key="group.title">
            <h2 class="mb-4 border-b border-gray-600 pb-4 pt-4 font-mono text-base text-white">
              {{ group.title }}
            </h2>

            <ul class="">
              <li v-for="section in group.sections" :key="section.title">
                <router-link
                  :to="sectionRoute(group, section)"
                  exact-active-class="border-r-[3px] border-[#e91e63]"
                  class="block px-3 py-1.5 font-open-sans text-sm text-[#e91e63] hover:underline"
                >
                  {{ section.title }}
                </router-link>
              </li>
            </ul>
          </template>
        </div>
        <div class="docs-content w-full px-12 pr-48 md:w-9/12">
          <Title
            :title="route.meta.section.title"
            :id="route.meta.section.title"
            :file="route.meta.section.file"
          ></Title>
          <div class="">
            <router-view></router-view>
          </div>

          <div class="flex border-t border-gray-600 pb-2.5 pt-4">
            <div class="w-[40%] text-left">
              <router-link v-if="previousSection !== null" class="text-sm text-white" :to="prevLink">
                ⇠ {{ previousSection.title }}
              </router-link>
            </div>

            <div class="w-[20%] text-center">
              <router-link v-if="homeSection !== null" :to="homeLink">
                <svg
                  class="inline-block h-4 w-4 fill-current"
                  version="1.1"
                  id="Capa_1"
                  xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  x="0px"
                  y="0px"
                  width="460.298px"
                  height="460.297px"
                  viewBox="0 0 460.298 460.297"
                  style="enable-background: new 0 0 460.298 460.297"
                  xml:space="preserve"
                >
                  <g>
                    <g>
                      <path
                        d="M230.149,120.939L65.986,256.274c0,0.191-0.048,0.472-0.144,0.855c-0.094,0.38-0.144,0.656-0.144,0.852v137.041c0,4.948,1.809,9.236,5.426,12.847c3.616,3.613,7.898,5.431,12.847,5.431h109.63V303.664h73.097v109.64h109.629c4.948,0,9.236-1.814,12.847-5.435c3.617-3.607,5.432-7.898,5.432-12.847V257.981c0-0.76-0.104-1.334-0.288-1.707L230.149,120.939z"
                      />
                      <path
                        d="M457.122,225.438L394.6,173.476V56.989c0-2.663-0.856-4.853-2.574-6.567c-1.704-1.712-3.894-2.568-6.563-2.568h-54.816c-2.666,0-4.855,0.856-6.57,2.568c-1.711,1.714-2.566,3.905-2.566,6.567v55.673l-69.662-58.245c-6.084-4.949-13.318-7.423-21.694-7.423c-8.375,0-15.608,2.474-21.698,7.423L3.172,225.438c-1.903,1.52-2.946,3.566-3.14,6.136c-0.193,2.568,0.472,4.811,1.997,6.713l17.701,21.128c1.525,1.712,3.521,2.759,5.996,3.142c2.285,0.192,4.57-0.476,6.855-1.998L230.149,95.817l197.57,164.741c1.526,1.328,3.521,1.991,5.996,1.991h0.858c2.471-0.376,4.463-1.43,5.996-3.138l17.703-21.125c1.522-1.906,2.189-4.145,1.991-6.716C460.068,229.007,459.021,226.961,457.122,225.438z"
                      />
                    </g>
                  </g>
                </svg>
              </router-link>
            </div>

            <div class="w-[40%] text-right">
              <router-link v-if="nextSection !== null" class="text-sm text-white" :to="nextLink">
                {{ nextSection.title }} ⇢
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
/** docs **/
.docs-content {
  @apply text-white;

  & hr {
    @apply border-0 border-b border-gray-600;
  }

  & > div > p {
    @apply mb-4 text-sm leading-6 text-gray-300;
  }

  & img {
    @apply mb-4;
  }

  & a {
    @apply text-[#e91e63]/90 hover:underline;
  }

  & > h3 {
    @apply mb-2 pt-2 font-mono text-lg;
  }

  & > h4 {
    @apply mb-2 pt-2 font-mono text-base italic;
  }

  & ul {
    @apply my-4 ml-6 list-outside list-disc text-sm;
  }
}

:not(pre) > code {
  font-size: 90%;
  @apply rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5];
}

.DocSearch-Button {
  @apply ml-0 w-full rounded-md bg-gray-800 pr-3 !ring-1 !ring-[#e91e63] hover:bg-gray-700;
}

.DocSearch-Search-Icon {
  @apply !mr-3 !text-sm !text-[#e91e63];
}

.DocSearch-Button-Keys {
  @apply ml-auto min-w-0;
}

.DocSearch-Button-Key {
  @apply mr-0 !w-auto  p-0 font-sans text-xs;
}

.DocSearch-Commands-Key {
  @apply p-0 ring-1 ring-[#e91e63];
}

.DocSearch-Form {
  @apply rounded-md;
}

.DocSearch-Input {
  @apply bg-transparent focus:outline-none focus:ring-0;
}

:root {
  --docsearch-icon-color: theme("colors.gray.300");

  --docsearch-modal-background: theme("colors.gray.900");
  --docsearch-modal-shadow: none;
  --docsearch-footer-background: theme("colors.gray.900");
  --docsearch-footer-shadow: none;
  --docsearch-key-gradient: none;
  --docsearch-key-shadow: none;
  --docsearch-key-pressed-shadow: none;

  --docsearch-hit-height: 56px;
  --docsearch-hit-color: theme("colors.gray.300");
  --docsearch-hit-active-color: #fff;
  --docsearch-hit-background: theme("colors.gray.900");
  --docsearch-hit-shadow: none;

  --docsearch-primary-color: #e91e63;
  --docsearch-text-color: theme("colors.gray.300");
  --docsearch-spacing: 10px;
  --docsearch-icon-stroke-width: 1.4;
  --docsearch-highlight-color: var(--docsearch-primary-color);
  --docsearch-muted-color: theme("colors.gray.300");
  --docsearch-container-background: rgba(101, 108, 133, 0.8);
  --docsearch-logo-color: #e91e63;

  --docsearch-searchbox-height: 56px;
  --docsearch-searchbox-background: theme("colors.gray.800");
  --docsearch-searchbox-focus-background: theme("colors.gray.800");
  --docsearch-searchbox-shadow: inset 0 0 0 1px var(--docsearch-primary-color);
}
</style>
