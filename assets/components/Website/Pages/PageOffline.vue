<script setup>
import Heading from "../PageHeading.vue";
import PageSection from "./PageSection.vue";
import { CheckIcon } from "@heroicons/vue/24/solid";
import TerminalStep from "./Offline/TerminalStep.vue";
import { CogIcon } from "@heroicons/vue/24/outline";

const steps = [
  {
    title: "Install Workshop Manager",
    description:
      "Download the workshop manager binary, move it to a directory in your path and run the verify command. The verify command will nudge you about any issues it might find.",
    lines: [
      "curl -O https://php-school.github.io/workshop-manager/workshop-manager.phar",
      "mv workshop-manager.phar /usr/local/bin/workshop-manager",
      "chmod +x /usr/local/bin/workshop-manager",
      "workshop-manager verify",
    ],
  },
  {
    title: "Using Workshop Manager",
    description:
      "Show the workshop manager available commands with descriptions. A handy reference for everything the workshop manager can do.",
    lines: ["workshop-manager list"],
  },
  {
    title: "Search for workshops",
    description: "Search for a workshop covering a particular topic.",
    lines: ["workshop-manager search <topic>", "//eg search for the PHP 8 workshop", "workshop-manager search php8"],
  },
  {
    title: "List all workshops",
    description: "List all available workshops.",
    lines: ["workshop-manager search"],
  },
  {
    title: "Install a Workshop",
    description: "",
    lines: [
      "workshop-manager install <workshopname>",
      "//eg install php8appreciate",
      "workshop-manager install php8appreciate",
    ],
  },
  {
    title: "Uninstall a Workshop",
    description: "",
    lines: [
      "workshop-manager uninstall <workshopname>",
      "//eg uninstall php8appreciate",
      "workshop-manager uninstall php8appreciate",
    ],
  },
];
</script>
<template>
  <div>
    <section class="items-stretch bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed">
      <Heading>
        <template v-slot:title>
          PHP School on the
          <br />
          Command Line
        </template>

        <template v-slot:description>
          In order to get started with PHP School workshops, you first need to install the workshop manager. Before we
          can do this we need to check you have a few things
        </template>
      </Heading>
    </section>
    <PageSection>
      <template #title>Getting Started</template>

      <div class="flex flex-wrap bg-gray-900 p-4 text-white">
        <ol class="mx-auto flex w-full flex-col">
          <li class="mb-12 flex w-full justify-between p-4">
            <div class="flex flex-col">
              <div class="mb-4 flex h-8 w-8 items-center justify-center rounded bg-slate-800 font-bold">
                <CogIcon class="h-6 w-6 !fill-none text-white" />
              </div>
              <div class="ml-4 flex-grow border-l border-solid border-slate-800"></div>
            </div>

            <div class="flex w-full flex-col">
              <div class="mr-8 w-full flex-1 px-4">
                <h4 class="mt-0 p-0 font-work-sans text-lg font-bold capitalize not-italic text-pink-600">
                  Requirements Check
                </h4>
                <p class="mt-4 text-sm text-gray-300">
                  In order to get started with PHP School workshops, you first need to install the workshop manager.
                  Before we can do this we need to check you have a few things:
                </p>
              </div>

              <ul class="m-0 mt-8 w-full list-none p-4 lg:m-0 lg:mt-0">
                <li class="flex p-2">
                  <CheckIcon fill="currentColor" class="mr-4 h-5 w-5 flex-shrink-0 text-green-500" />
                  <p>
                    You will need PHP with a version of at least 7.1 although we recommend using the latest available.
                  </p>
                </li>
                <li class="flex p-2">
                  <CheckIcon fill="currentColor" class="mr-4 h-5 w-5 flex-shrink-0 text-green-500" />
                  <p>
                    You will also need a Text Editor so you can work through the workshops. You can try Atom or Sublime
                    if you don't already have one.
                  </p>
                </li>
              </ul>

              <div class="mr-8 w-full flex-1 px-4">
                <p class="mt-4 text-sm text-gray-300">
                  Once the above are satisfied, run the following commands in your terminal, to install the workshop
                  manager.
                </p>
              </div>
            </div>
          </li>

          <TerminalStep
            v-for="(step, i) in steps"
            :key="i"
            :step="i + 1"
            :title="step.title"
            :description="step.description"
            :lines="step.lines"
          />
        </ol>
      </div>
    </PageSection>
    <PageSection>
      <template #title>Troubleshooting</template>
    </PageSection>
  </div>
</template>
