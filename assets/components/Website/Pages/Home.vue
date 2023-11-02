<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Button from "../Button.vue";
import GitHubIcon from "../../Icons/GitHubIcon.vue";
import MockWorkshopExerciseList from "./Home/Mocks/MockWorkshopExerciseList.vue";
import MockWorkshopList from "./Home/Mocks/MockWorkshopList.vue";
import MockEditor from "./Home/Mocks/MockEditor.vue";
import MockProblemModal from "./Home/Mocks/MockProblemModal.vue";
import MockResults from "./Home/Mocks/MockResults.vue";
import MockPassNotification from "./Home/Mocks/MockPassNotification.vue";
import MockOfficialSolution from "./Home/Mocks/MockOfficialSolution.vue";
import MockProgress from "./Home/Mocks/MockProgress.vue";
import HomeHero from "./Home/Hero.vue";
import InfoSection from "./Home/InfoSection.vue";
import GettingStarted from "./Home/Section/GettingStarted.vue";
import TheWorkshops from "./Home/Section/TheWorkshops.vue";
import BuildYourOwn from "./Home/Section/BuildYourOwn.vue";
import Terminal from "./Home/Section/TerminalSection.vue";
import {ArrowPathIcon, SparklesIcon} from "@heroicons/vue/24/solid";

const userInput = ref('');
const showInitialSetup = ref(true);
const showCorrectInput = ref(false);
const showWrongInput = ref(false);

const userInputKeyup = (event) => {
  if (event.code === 'Enter') {
    return checkInput();
  }

  showInitialSetup.value = true;
  showCorrectInput.value = false;
  showWrongInput.value = false;
};

const checkInput = () => {
  if (userInput.value === 'echo "Hello World";') {
    showInitialSetup.value = false;
    showCorrectInput.value = true;
    showWrongInput.value = false;
  } else {
    showInitialSetup.value = false;
    showCorrectInput.value = false;
    showWrongInput.value = true;
  }
};


const fadeIns = ref([]);

function checkImages() {
  fadeIns.value.forEach((container) => {
    const rect = container.getBoundingClientRect();
    const windowHeight = window.innerHeight;

    if (rect.top < windowHeight * 0.8) {
      container.classList.remove('opacity-0', 'translate-y-6', 'translate-x-10');
      container.classList.add('opacity-1')
    }
  });
}

onMounted(() => {

  fadeIns.value = document.querySelectorAll(".fadeIn");

  window.addEventListener('scroll', checkImages);
  window.addEventListener('resize', checkImages);
  checkImages();
});

</script>

<template>
  <header class="flex flex-col w-full ">
    <HomeHero />
  </header>


  <!-- Information Section -->
  <section class="bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed items-stretch pb-10 pt-28 sm:pt-40">

    <!-- Section 1 -->
    <InfoSection>
      <template #left>
        <div class="mx-auto w-full lg:w-2/3">
          <div class="items-center justify-center flex relative">
            <img class="cover " src="../../../img/cloud/pattern-bg-square.svg" alt="">
            <div class="absolute left-1  md:left-auto">
              <div class="fadeIn opacity-0 translate-y-6 transition-all ease-in duration-[1000ms]">
                <MockWorkshopExerciseList />
              </div>
            </div>
            <div
              class="absolute left-[80px] top-[90] xs:left-[120px] sm:left-[250px] md:left-[60%] md:top-[80] lg:-right-[10%] lg:top-[30] xl:top-[90] ">
              <div class="fadeIn opacity-0 translate-x-10 transition-all ease-in duration-[1000ms]">
                <MockWorkshopList />
              </div>
            </div>
            <div class=" hidden absolute sm:block -bottom-36 right-10  lg:-bottom-60 lg:left-10 2xl:left-60 z-50">
              <!-- <div class="ellipse bobbing-animation"></div> -->
              <img class="bobbing-animation w-auto h-auto " src="../../../img/cloud/php-elephant-bandana.svg" alt=""
                srcset="">
            </div>
          </div>
        </div>
      </template>

      <template #right>
        <div class="w-full lg:w-1/3 space-y-8 sm:px-5 mt-28 sm:mt-40 text-left">
          <h2 class="font-work-sans text-white font-bold text-5xl">Open Source PHP Workshops</h2>
          <p class="font-work-sans text-white font-base text-lg">PHP School is a set of workshops each
            designed to teach a specific topic, tool, technology. Some beginner, some advanced. Each workshop consists of
            multiple exercises where your task is to code a solution to solve a problem. All our workshops are open
            source, you can contribute to them also, with bug fixes, new exercises, or your own workshop.</p>
          <div class="flex justify-start">
            <Button href="/cloud">GET STARTED</Button>
          </div>
        </div>
      </template>
    </InfoSection>

      <!-- Section 2 -->
      <InfoSection>
        <template #left>
          <div class="w-full lg:w-1/3 space-y-8 sm:px-5 text-left">
            <h2 class="font-work-sans text-white font-bold text-5xl">Online Browser Based IDE</h2>
            <p class="font-work-sans text-white font-base text-lg">Login in with your GitHub account, select a
              workshop and an exercise and jump straight in to our web based text editor (IDE). No complicated setup, no
              need to install tools, dependencies and text editors. Just jump in and start coding.</p>
            <div class="flex justify-start">
              <Button href="/cloud" class="flex items-center">
                <GitHubIcon class="h-6 w-6 mr-2" /><span>Log In with github</span>
              </Button>
            </div>
          </div>
        </template>

      <template #right>
        <div class="mx-auto w-full lg:w-2/3 mt-16 lg:mt-0">
          <div class=" items-center justify-center flex relative ">
            <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="">
            <div
              class="absolute flex items-center justify-center h-[500px] w-[800px] fadeIn opacity-0 translate-y-6 transition-all ease-in duration-[1000ms]">
              <MockEditor class="" />
            </div>
            <div
              class="absolute right-10 lg:-right-16 xl:right-14   top-[90] md:top-[80] lg:top-[30] xl:top-[90] md:w-auto md:h-auto">
              <div class="fadeIn opacity-0 translate-x-10 transition-all ease-in duration-[1000ms]">
                <MockWorkshopList />
              </div>
            </div>

          </div>
        </div>
      </template>
    </InfoSection>

    <!-- Section 3 -->
    <InfoSection>
      <template #left>
        <div class="mx-auto  w-full lg:w-2/3  relative">
          <div class=" items-center justify-center flex relative">
            <img class="" src="../../../img/cloud/pattern-bg-square-alt.svg" alt="">
            <div class="absolute left-1  md:left-auto">
              <MockProblemModal />
            </div>
            <div
              class="absolute left-[80px] top-[90] xs:left-[120px] sm:left-[250px] md:left-[60%] md:top-[80] lg:-right-[10%] lg:top-[30] xl:top-[90] ">
              <MockProgress class="flex w-[400px]" />
            </div>
            <div class=" hidden absolute sm:block  -bottom-36 right-10   lg:-bottom-60  lg:left-10 2xl:left-60 z-50">
              <!-- <div class="ellipse bobbing-animation"></div> -->
              <!-- <img class="bobbing-animation w-auto h-auto" src="/img/cloud/elephant-swim.svg" alt="" srcset=""> -->
            </div>
          </div>
        </div>
      </template>

      <template #right>
        <div class="w-full lg:w-1/3 space-y-8 sm:px-5 mt-28 sm:mt-40 text-left">
          <h2 class="font-work-sans text-white font-bold text-5xl">Work on practical assignments</h2>
          <p class="font-work-sans text-white font-base text-lg">Level up your problem solving skills whilst tackling
            practical problems that you will be sure to encounter in your chosen path as a software developer. Each
            exercise comes with a description, code samples to get you started, links to documentation and other resources
            to help you solve the problem. </p>
          <div class="flex justify-start">
            <Button href="/cloud">GET STARTED</Button>
          </div>
        </div>
      </template>
    </InfoSection>

    <!-- Section 4 -->
    <InfoSection :divider="false">
      <template #left>
        <div class="w-full lg:w-1/3 space-y-8 sm:px-5 text-left">
          <h2 class="font-work-sans text-white font-bold text-5xl">Get instant feedback and track your progress
          </h2>
          <p class="font-work-sans text-white font-base text-lg">You can test your solution at any time and you will get
            instance feedback, if you get it wrong, we’ll show you how and what went wrong versus what we expected. You’ll
            know instantly if you need to start fresh or make a simple tweak. When you solve the exercise, your profile is
            updated and the exercise is marked as completed. Before proceeding you have the opportunity to see our
            official solution so you can compare notes and optimise your own solution. Maybe your solution is better!
            <br> <br>
            Try writing: <code>echo "Hello World";</code> in the input below to see how we verify your solution and
            provide
            feedback.
          </p>
          <div class="flex justify-start">
            <div class="relative w-full flex items-center">
              <input type="text" placeholder="Type Something..." v-model="userInput"
                class="w-full p-5 font-work-sans text-base font-bold text-gray-900 rounded-2xl  focus:outline-none focus:border-pink-500 focus:ring focus:ring-pink-500"
                @keyup="userInputKeyup" />


              <button @click="checkInput"  class="absolute right-1.5 flex items-center justify-center mt-0 px-2 w-24 text-white text-sm  rounded-xl bg-gradient-to-r from-pink-600 to-purple-500 hover:bg-[#aa1145] transition-all duration-300 ease-in hover:opacity-90 h-[52px]" >
                <span>Verify</span>
                <SparklesIcon v-cloak class="ml-2 w-5 h-5"/>
              </button>
            </div>
          </div>
        </div>
      </template>

      <template #right>
        <div class="mx-auto w-full lg:w-2/3 mt-16 lg:mt-0">

          <!-- Initial set up -->
          <div class=" items-center justify-center flex relative" id="feedback-section-initial" v-if="showInitialSetup">
            <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="">
            <div class="absolute flex items-center justify-center h-[500px] w-[800px]">
              <MockEditor />
            </div>
            <div class="absolute flex items-center justify-center">
              <MockProblemModal />
            </div>
          </div>

          <!-- correct input -->
          <div class=" items-center justify-center relative flex" id="feedback-section-correct" v-if="showCorrectInput">
            <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="">
            <div class="absolute flex items-center justify-center h-[500px] w-[800px]">
              <MockEditor />
              <div class="absolute flex  top-5 sm:top-10 lg:top-8 xl:top-12">
                <MockPassNotification />
              </div>
              <div class="absolute flex  -left-3 sm:bottom-8 sm:-left-4 md:-left-5 md:bottom-8 xl:bottom-14">
                <MockOfficialSolution class="h-[250px] w-[400px]" />
              </div>
            </div>

          </div>
          <!-- wrong input -->
          <div class=" items-center justify-center relative flex shake" id="feedback-section-wrong" v-if="showWrongInput">
            <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="">
            <div class="absolute flex items-center justify-center h-[500px] w-[800px]">
              <MockEditor />
            </div>
            <div class="absolute flex items-center ml-8">
              <MockResults />
            </div>
          </div>
        </div>
      </template>
    </InfoSection>
  </section>

  <GettingStarted />
  <TheWorkshops />
  <BuildYourOwn />
  <Terminal />
</template>