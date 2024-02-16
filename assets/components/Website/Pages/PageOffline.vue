<script setup>
import Heading from "../PageHeading.vue";
import PageSection from "./PageSection.vue";
import { CheckIcon } from "@heroicons/vue/24/solid";
import TerminalStep from "./Offline/TerminalStep.vue";
import { CogIcon } from "@heroicons/vue/24/outline";
import TerminalDisplay from "./Offline/TerminalDisplay.vue";
import WindowsLogo from "../../Icons/WindowsLogo.vue";
import AppleLogo from "../../Icons/AppleLogo.vue";
import LinuxLogo from "../../Icons/LinuxLogo.vue";
import {Tab, TabGroup, TabList, TabPanel, TabPanels, TransitionRoot} from "@headlessui/vue";
import SupportStep from "./Offline/SupportStep.vue";
import WebNote from "../WebNote.vue";

const steps = [
  {
    title: "Install Workshop Manager",
    description: "Download the workshop manager binary, move it to a directory in your path and run the verify command. The verify command will nudge you about any issues it might find.",
    lines: [
      "curl -O https://php-school.github.io/workshop-manager/workshop-manager.phar",
      "mv workshop-manager.phar /usr/local/bin/workshop-manager",
      "chmod +x /usr/local/bin/workshop-manager",
      "workshop-manager verify",
    ],
  },
  {
    title: "Using Workshop Manager",
    description: "Show the workshop manager available commands with descriptions. A handy reference for everything the workshop manager can do.",
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
    lines: ["workshop-manager install <workshopname>", "//eg install php8appreciate", "workshop-manager install php8appreciate"],
  },
  {
    title: "Uninstall a Workshop",
    description: "",
    lines: ["workshop-manager uninstall <workshopname>", "//eg uninstall php8appreciate", "workshop-manager uninstall php8appreciate"],
  },
];

const macSteps = [
  {
    title: "Check your PHP version",
    description: "If you have a PHP version less than 7.1, you will need to update it to at least 7.1, you can do so with the following commands:",
    lines: [
      "/bin/bash -c \"$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)\"",
      "brew install php"
    ],
  },
  {
    title: "Check PHP School's bin directory is available in $PATH",
    description: "After installing a workshop using the workshop manager you may find it's not available to run immediately. If this happens the simplest remedy is to make sure PHP School's workshop bin directory is available in the $PATH environment variable.\n" +
        "\n" +
        "You can check this with workshop-manager verify which will also provide the relevant details on how to resolve the issue.\n" +
        "\n" +
        "To learn more about the $PATH environment, click here.",
    lines: [],
  },
];

const linux = [
    "sudo apt-get install software-properties-common",
    "sudo add-apt-repository ppa:ondrej/php",
    "sudo apt-get install php7.1"
]
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
          In order to get started with PHP School workshops, you first need to install the workshop manager. Before we can do this we need to check you have a few things
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
                <h4 class="mt-0 p-0 font-work-sans text-lg font-bold capitalize not-italic text-pink-600">Requirements Check</h4>
                <p class="mt-4 text-sm text-gray-300">
                  In order to get started with PHP School workshops, you first need to install the workshop manager. Before we can do this we need to check you have a few things:
                </p>
              </div>

              <ul class="m-0 mt-8 w-full list-none p-4 lg:m-0 lg:mt-0">
                <li class="flex p-2">
                  <CheckIcon fill="currentColor" class="mr-4 h-5 w-5 flex-shrink-0 text-green-500" />
                  <p>You will need PHP with a version of at least 7.1 although we recommend using the latest available.</p>
                </li>
                <li class="flex p-2">
                  <CheckIcon fill="currentColor" class="mr-4 h-5 w-5 flex-shrink-0 text-green-500" />
                  <p>You will also need a Text Editor so you can work through the workshops. You can try Atom or Sublime if you don't already have one.</p>
                </li>
              </ul>

              <div class="mr-8 w-full flex-1 px-4">
                <p class="mt-4 text-sm text-gray-300">Once the above are satisfied, run the following commands in your terminal, to install the workshop manager.</p>
              </div>
            </div>
          </li>

          <TerminalStep v-for="(step, i) in steps" :key="i" :step="i + 1" :title="step.title" :description="step.description" :lines="step.lines" />
        </ol>
      </div>
    </PageSection>
    <PageSection>
      <template #title>Troubleshooting</template>

      <div class="w-full">
        <TabGroup>
          <TabList class="flex my-20">
            <Tab as="template" v-slot="{ selected }">
              <div class="w-1/3 flex flex-col justify-center items-center cursor-pointer group focus:outline-none">
                <AppleLogo class="h-[200px] aspect-square " :class="{ 'text-white group-hover:text-[#e91e63]': !selected, 'text-[#e91e63]': selected }"></AppleLogo>
                <h3 class="mt-4 p-0 font-work-sans text-2xl capitalize not-italic" :class="{ 'text-white group-hover:text-pink-600': !selected, 'text-[#e91e63]': selected }">Apple Mac</h3>
              </div>
            </Tab>
            <Tab as="template" v-slot="{ selected }">
              <div class="w-1/3 flex flex-col justify-center items-center cursor-pointer group focus:outline-none">
                <LinuxLogo class="h-[200px] aspect-square" :class="{ 'text-white group-hover:text-[#e91e63]': !selected, 'text-[#e91e63]': selected }"></LinuxLogo>
                <h3 class="mt-4 p-0 font-work-sans text-2xl capitalize not-italic" :class="{ 'text-white group-hover:text-pink-600': !selected, 'text-[#e91e63]': selected }">Linux</h3>
              </div>
            </Tab>
            <Tab as="template" v-slot="{ selected }">
              <div class="w-1/3 flex flex-col justify-center items-center cursor-pointer group focus:outline-none">
                <WindowsLogo class="h-[200px] aspect-square" :class="{ 'text-white group-hover:text-[#e91e63]': !selected, 'text-[#e91e63]': selected }"></WindowsLogo>
                <h3 class="mt-4 p-0 font-work-sans text-2xl capitalize not-italic" :class="{ 'text-white group-hover:text-pink-600 ': !selected, 'text-[#e91e63]': selected }">Windows</h3>
              </div>
            </Tab>
          </TabList>
          <TabPanels>
            <TabPanel v-slot="{ selected }">
              <h3 class="mb-8 p-0 font-work-sans text-5xl capitalize not-italic text-pink-600">Apple Mac</h3>
              <p class="mb-8 text-white">Common issues with Mac OSX installations include not having a new enough version of PHP and not having Composer available.</p>
              <SupportStep :step="1" :title="'Check your PHP version'" :lines="[
                '/bin/bash -c &quot;$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)&quot;',
                'brew install php'
              ]">
                  <template #description>
                    <p>If you have a PHP version less than 8.1, you will need to update it to at least 8.1, you can do so with the following commands</p>

                    <WebNote type="info">You can check your PHP version with <code>php -v</code></WebNote>
                  </template>
              </SupportStep>
              <SupportStep :step="2" :title="'Check PHP School\'s bin directory is available in $PATH'">
                <template #description>
                  <p>After installing a workshop using the workshop manager you may find it's not available to run immediately. If this happens the simplest remedy is to make sure PHP School's workshop bin directory is available in the <code>$PATH</code> environment variable.

                    You can check this with <code>workshop-manager</code> verify which will also provide the relevant details on how to resolve the issue.

                    To learn more about the <code>$PATH</code> environment, click <a class="text-[#e91e63] hover:underline" href="https://en.wikipedia.org/wiki/PATH_(variable)">here</a>.</p>
                </template>
              </SupportStep>
            </TabPanel>
            <TabPanel v-slot="{ selected }">
              <h3 class="mb-8 p-0 font-work-sans text-5xl capitalize not-italic text-pink-600">Linux</h3>
              <p class="mb-8 text-white">Common issues with Linux installations include not having a new enough version of PHP and not having Composer available.</p>
              <SupportStep :step="1" :title="'Check your PHP version'" :lines="[
                'sudo apt-get update && sudo apt-get upgrade',
                'sudo apt-get install software-properties-common',
                'sudo add-apt-repository ppa:ondrej/php',
                'sudo apt-get update',
                'sudo apt-get install php8.3',
              ]">
                <template #description>
                  <p>If you have a PHP version less than 8.1, you will need to update it to at least 8.1, you can do so with the following commands</p>

                  <WebNote type="info">You can check your PHP version with <code>php -v</code></WebNote>
                </template>
              </SupportStep>
              <SupportStep :step="2" :title="'Check PHP School\'s bin directory is available in $PATH'">
                <template #description>
                  <p>After installing a workshop using the workshop manager you may find it's not available to run immediately. If this happens the simplest remedy is to make sure PHP School's workshop bin directory is available in the <code>$PATH</code> environment variable.

                    You can check this with <code>workshop-manager</code> verify which will also provide the relevant details on how to resolve the issue.

                    To learn more about the <code>$PATH</code> environment, click <a class="text-[#e91e63] hover:underline" href="https://en.wikipedia.org/wiki/PATH_(variable)">here</a>.</p>
                </template>
              </SupportStep>
            </TabPanel>
            <TabPanel>
              <h3 class="mb-8 p-0 font-work-sans text-5xl capitalize not-italic text-pink-600">Windows</h3>
              <p class="mb-8 text-white">Windows is a difficult system to cater for in the PHP world. Unfortunately, it has various differences on the command line and console emulators which PHP unfortunately doesn't support. The best way to get PHP School Workshops running is to install Cygwin + ConEmu. Once the initial setup of these are complete, the process of installing workshops is the same as Linux and Mac OSX operating systems.</p>
              <SupportStep :step="1" :title="'Check if Cygwin is installed'">
                <template #description>
                  <p>If not, follow the instructions below:</p>
                  <ul class="list-decimal list-inside ml-2 mt-4">
                    <li class="list-item p-1">Head on over to <a class="text-[#e91e63] hover:underline" href="https://cygwin.com/install.html" target="_blank">https://cygwin.com/install.html</a> and grab the latest installer for your system, 32-bit or 64-bit.</li>
                    <li class="list-item p-1">Run the installer and chose the default values until the package selection point.</li>
                    <li class="list-item p-1">Ensure you choose to install ALL PHP packages. We also recommend installing GIT and VIM to complete your CLI experience.</li>
                    <li class="list-item p-1">Complete the installation.</li>
                  </ul>
                </template>
              </SupportStep>
              <SupportStep :step="2" :title="'Check if ConEmu is installed'">
                <template #description>
                  <ul class="list-decimal list-inside ml-2 mt-4">
                    <li class="list-item p-1">Grab the installer from <a class="text-[#e91e63] hover:underline" href="https://conemu.github.io/" target="_blank">https://conemu.github.io/</a>.</li>
                    <li class="list-item p-1">Run the installer and open ConEmu.</li>
                    <li class="list-item p-1">Select Cygwin Bash</li>
                  </ul>
                </template>
              </SupportStep>
              <SupportStep :step="3" :title="'Check PHP School\'s bin directory is available in $PATH'">
                <template #description>
                  <p>After installing a workshop using the workshop manager you may find it's not available to run immediately. If this happens the simplest remedy is to make sure PHP School's workshop bin directory is available in the <code>$PATH</code> environment variable.

                    You can check this with <code>workshop-manager</code> verify which will also provide the relevant details on how to resolve the issue.

                    To learn more about the <code>$PATH</code> environment, click <a class="text-[#e91e63] hover:underline" href="https://en.wikipedia.org/wiki/PATH_(variable)">here</a>.</p>
                </template>
              </SupportStep>
            </TabPanel>
          </TabPanels>
        </TabGroup>
      </div>
    </PageSection>
  </div>
</template>
