<script setup>
import Terminal from "./Home/Section/TerminalSection.vue";
import Heading from "./Home/Heading.vue";
import HomeSection from "./Home/Section.vue";
</script>

<template>
    <div>
        <section class="bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed items-stretch">
            <Heading>
                <template v-slot:title>
                    PHP School on the <br>Command Line
                </template>

                <template v-slot:description>
                    In order to get started with PHP School workshops, you first need to install the workshop manager. Before we can do this we need to check you have a few things
                </template>
            </Heading>
        </section>
        <Terminal />
        <HomeSection>
            <template #title>Troubleshooting</template>
        </HomeSection>
    </div>
</template>
