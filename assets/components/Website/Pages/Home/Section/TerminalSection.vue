<script setup>
import HomeSection from "../Section.vue";
import {CogIcon} from "@heroicons/vue/24/outline";
import {CheckIcon} from "@heroicons/vue/24/solid";
import TerminalStep from "./TerminalStep.vue";

const steps = [
    {
        title: 'Install Workshop Manager',
        description: 'Download the workshop manager binary, move it to a directory in your path and run the verify command. The verify command will nudge you about any issues it might find.',
        lines: [
            'curl -O https://php-school.github.io/workshop-manager/workshop-manager.phar',
            'mv workshop-manager.phar /usr/local/bin/workshop-manager',
            'chmod +x /usr/local/bin/workshop-manager',
            'workshop-manager verify',
        ]
    },
    {
        title: 'Using Workshop Manager',
        description: 'Show the workshop manager available commands with descriptions. A handy reference for everything the workshop manager can do.',
        lines: [
            'workshop-manager list',
        ]
    },
    {
        title: 'Search for workshops',
        description: 'Search for a workshop covering a particular topic.',
        lines: [
            'workshop-manager search <topic>',
            '//eg search for the PHP 8 workshop',
            'workshop-manager search php8'
        ]
    },
    {
        title: 'List all workshops',
        description: 'List all available workshops.',
        lines: [
            'workshop-manager search',
        ]
    },
    {
        title: 'Install a Workshop',
        description: '',
        lines: [
            'workshop-manager install <workshopname>',
            '//eg install php8appreciate',
            'workshop-manager install php8appreciate',
        ]
    },
    {
        title: 'Uninstall a Workshop',
        description: '',
        lines: [
            'workshop-manager uninstall <workshopname>',
            '//eg uninstall php8appreciate',
            'workshop-manager uninstall php8appreciate',
        ]
    },
]

</script>

<template>
    <HomeSection>
        <template #title>Getting Started</template>

        <div class="bg-gray-900 text-white p-4 flex flex-wrap">
            <ol class="mx-auto w-full flex flex-col">
                <li class="w-full p-4 flex justify-between mb-12">
                    <div class="flex flex-col">
                        <div class="bg-slate-800 rounded font-bold w-8 h-8 flex items-center justify-center mb-4">
                            <CogIcon class="h-6 w-6 text-white !fill-none" />
                        </div>
                        <div class="border-l border-solid border-slate-800 flex-grow ml-4"></div>
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex-1 px-4 mr-8 w-full">
                            <h4 class="text-lg font-work-sans font-bold capitalize text-pink-600 mt-0 p-0  not-italic">Requirements Check</h4>
                            <p class="text-sm text-gray-300 mt-4">In order to get started with PHP School workshops, you first need to install the workshop manager. Before we can do this we need to check you have a few things:</p>
                        </div>

                        <ul class="list-none p-4 w-full m-0 mt-8 lg:m-0 lg:mt-0">
                            <li class="flex p-2">
                                <CheckIcon fill="currentColor" class="flex-shrink-0 w-5 h-5 text-green-500 mr-4" />
                                <p>You will need PHP with a version of at least 7.1 although we recommend using the latest available.</p>
                            </li>
                            <li class="flex p-2">
                                <CheckIcon fill="currentColor" class="flex-shrink-0 w-5 h-5 text-green-500 mr-4" />
                                <p>You will also need a Text Editor so you can work through the workshops. You can try Atom or Sublime if you don't already have one.</p>
                            </li>
                        </ul>

                        <div class="flex-1 px-4 mr-8 w-full">
                            <p class="text-sm text-gray-300 mt-4">Once the above are satisfied, run the following commands in your terminal, to install the workshop manager.</p>
                        </div>
                    </div>
                </li>

                <TerminalStep v-for="(step, i) in steps" :step="i + 1" :title="step.title" :description="step.description" :lines="step.lines" />
            </ol>
        </div>
    </HomeSection>
</template>