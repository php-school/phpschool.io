<script setup>
import PageSection from "../../PageSection.vue";
import BuildList from "./BuildList.vue";

const build = {
  docs: [
    "Learn the fundamentals of the PHP Workshop framework.",
    "Follow along with a tutorial to create your own workshop.",
    "Explore more in-depth documentation; how to build checks, results, renderers and more.",
    "Learn about the event system and advanced topics such as code patching.",
    "Understand the verification system and learn how to check that a students' submission passes the task whilst using dynamic inputs.",
  ],
  build: [
    "Write descriptions, give hints and links for the student to explore.",
    "Define the inputs and outputs, such as writing to files, generating output, communicating with a server or API and whatever else you can think of.",
    "Write the code to verify the student's submission, and give them feedback.",
    "Use our super simple system to write unit tests for your exercises.",
  ],
};
</script>

<template>
  <PageSection>
    <template #title>
      Build your own workshop
      <br />
      + Host it on PHP School
    </template>
    <div class="flex flex-wrap bg-gray-900 p-4 text-white">
      <div class="mb-4 flex flex-wrap">
        <div class="w-full p-4 sm:w-1/2">
          <h3 class="mb-5 font-open-sans text-lg font-bold text-white">Explore the Documentation</h3>
          <p class="font-open-sans text-base leading-relaxed text-white">
            We're sure you're eager to jump in and start coding, but you're gonna have to slow down a little. The first step to creating your own workshop is to read the documentation:
          </p>
          <BuildList :list="build.docs"></BuildList>
        </div>

        <div class="w-full p-4 sm:w-1/2">
          <h3 class="mb-5 font-open-sans text-lg font-bold text-white">Build your workshop</h3>
          <p class="font-open-sans text-base leading-relaxed text-white">
            Now it's time to build - you will create your base application, define a theme and a topic, design a set of exercises which progress in difficulty and introduce new and advanced topics to
            the student. All in PHP.
          </p>
          <BuildList :list="build.build"></BuildList>
        </div>
      </div>

      <div class="mb-10 flex flex-wrap">
        <div class="w-full p-4 sm:w-1/2">
          <h3 class="mb-5 font-open-sans text-lg font-bold text-white">Submission and review</h3>
          <p class="font-open-sans text-base leading-relaxed text-white">
            Once you've built your workshop, you
            <router-link to="/submit" class="text-[#e91e63] hover:underline">submit</router-link>
            it to us. We will perform a few basic automated checks and then a further manual review. Once it's approved, it will be installable via
            <code>workshop-manager</code>
            and then later on, as part of PHP School Online!
          </p>
        </div>

        <div class="w-full p-4 sm:w-1/2">
          <h3 class="mb-5 font-open-sans text-lg font-bold text-white">Open to the world</h3>
          <p class="font-open-sans text-base leading-relaxed text-white">
            Your workshop is available for the world to see, use and learn from. Not only that but it's open source, it's available for other developers to update, fix, improve the copy, translate and
            so on!
          </p>
        </div>
      </div>
    </div>
  </PageSection>
</template>
