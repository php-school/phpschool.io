<script setup>
import {ref, onMounted, onUnmounted} from 'vue';
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

import {SparklesIcon} from '@heroicons/vue/24/solid'

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

const transitions = {
    exerciseList: ref(),
    workshopList: ref(),
    editor: ref(),
    editorWorkshopList: ref(),
}

const debounce = function(func, wait, immediate) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        const later = () => {
            timeout = null;
            if (!immediate) {
                func.apply(context, args);
            }
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) {
            func.apply(context, args);
        }
    };
};

function checkImages() {
    Object.keys(transitions).forEach((key) => {
        const container = transitions[key].value;
        const rect = container.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        if (rect.top < windowHeight * 0.8) {
            container.classList.remove('opacity-0', 'translate-y-6', 'translate-x-10');
            container.classList.add('opacity-1')
        }
    });
}

const debouncedCheckImages = debounce(checkImages, 500);

onMounted(() => {
    window.addEventListener('scroll', checkImages);
    window.addEventListener('resize', debouncedCheckImages);
    checkImages();
});

onUnmounted(() => {
    window.removeEventListener('scroll', checkImages);
    window.removeEventListener('resize', debouncedCheckImages);
});

</script>

<template>
  <div>
      <header class="flex flex-col w-full ">
          <HomeHero />
      </header>


      <!-- Information Section -->
      <section class="bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed items-stretch py-20 sm:py-40 overflow-hidden">

          <!-- Section 1 -->
          <InfoSection>
              <template #left>
                  <div class="mx-auto w-full lg:w-2/3">
                      <div class="items-center justify-center flex relative ">
                          <img class="cover " src="../../../img/cloud/pattern-bg-square.svg" alt="">
                          <div class="absolute mx-auto md:left-auto scale-75 sm:scale-100">
                              <div :ref="transitions.exerciseList" class="fadeIn opacity-0 translate-y-6 transition-all ease-in duration-[1000ms] lg:mb-36">
                                  <MockWorkshopExerciseList />
                              </div>
                          </div>
                          <div
                                  class="absolute -right-4  top-1/4  md:left-4  md:top-[80] lg:top-[30] xl:top-[90] scale-75 sm:scale-95 md:scale-100">
                              <div :ref="transitions.workshopList" class="fadeIn opacity-0 translate-x-10 transition-all ease-in duration-[1000ms]">
                                  <MockWorkshopList />
                              </div>
                          </div>
                          <div class=" hidden absolute sm:block -bottom-36 right-10  lg:-bottom-60 lg:left-10 2xl:left-60 z-50">
                              <!-- <div class="ellipse bobbing-animation"></div> -->
                              <img class="bobbing-animation w-auto h-auto " src="../../../img/cloud/php-elephant-bandana.png" alt=""
                                   srcset="">
                          </div>
                      </div>
                  </div>
              </template>

              <template #right>
                  <div class="w-full lg:w-1/3 space-y-8 sm:px-5 text-left mt-28 md:mt-36 lg:mt-0">
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
                  <div class="w-full lg:w-1/3 space-y-8 sm:px-5 text-left mt-8 lg:mt-0 order-2 lg:order-1">
                      <h2 class="font-work-sans text-white font-bold text-5xl balanced">Online Browser Based IDE</h2>
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
                  <div class="mx-auto w-full lg:w-2/3 order-1 lg:order-1">
                      <div class=" items-center justify-center flex relative ">
                          <img class="cover sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="">
                          <div :ref="transitions.editor" class="absolute flex md:items-center md:justify-center w-[350px] h-[400px] sm:h-[500px] sm:w-[600px] md:h-[500px] md:w-[700px] lg:scale-[0.9] xl:scale-100 xl:h-[500px] xl:w-[800px] fadeIn opacity-0 translate-y-6 transition-all ease-in duration-[1000ms]">
                              <MockEditor class="overflow-hidden " />
                          </div>
                          <div :ref="transitions.editorWorkshopList" class="absolute -right-9  top-1/3  sm:top-1/2 sm:right-2 md:top-1/3 scale-75 xl:scale-100">
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
                  <div class="mx-auto  w-full lg:w-2/3  relative mt-20">
                      <div class=" items-center justify-center flex relative">
                          <img class="" src="../../../img/cloud/pattern-bg-square-alt.svg" alt="">
                          <div class="absolute">
                              <MockProblemModal />
                          </div>
                          <div
                                  class=" hidden sm:flex absolute -right-9  top-1/3  sm:top-1/3 sm:-right-2 md:top-1/3 scale-75 sm:scale-100">
                              <MockProgress class="" />
                          </div>
                          <div class=" hidden absolute sm:block  -bottom-36 -left-10   lg:-bottom-44   lg:-left-5 z-50">
                              <!-- <div class="ellipse bobbing-animation"></div> -->
                              <img class="bobbing-animation w-auto h-auto" src="/img/cloud/elephant-swim.png" alt="" srcset="">
                          </div>
                      </div>
                  </div>
              </template>

              <template #right>
                  <div class="w-full lg:w-1/3 space-y-8 sm:px-5 mt-28 sm:mt-40 text-left lg:mb-40">
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
                  <div class="w-full lg:w-1/3 space-y-8 sm:px-5 text-left order-2 lg:order-1 mt-8">
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
                          <div class="relative w-full ">
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
                  <div class="mx-auto w-full lg:w-2/3 lg:mt-0 order-1 lg:order-2">

                      <!-- Initial set up -->
                      <div class=" items-center justify-center flex relative" id="feedback-section-initial" v-if="showInitialSetup">
                          <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="">
                          <div class="absolute flex md:items-center md:justify-center w-[350px] h-[400px] sm:h-[500px] sm:w-[600px] md:h-[500px] md:w-[700px] lg:scale-[0.9] xl:scale-100 xl:h-[500px] xl:w-[800px] ">
                              <MockEditor />
                          </div>
                          <div class="absolute scale-75">
                              <MockProblemModal />
                          </div>
                      </div>

                      <!-- correct input -->
                      <div class=" items-center justify-center relative flex" id="feedback-section-correct" v-if="showCorrectInput">
                          <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="">
                          <div class="absolute flex md:items-center md:justify-center w-[350px] h-[400px] sm:h-[500px] sm:w-[600px] md:h-[500px] md:w-[700px] lg:scale-[0.9] xl:scale-100 xl:h-[500px] xl:w-[800px]">
                              <MockEditor />
                              <div class="absolute top-5 md:top-10 lg:top-8 xl:top-12 left-1/2 transform -translate-x-1/2 md:w-full md:scale-105 xl:px-4">
                                  <MockPassNotification  class="w-full shadow-[-10px_10px_0_rgba(0,0,0,0.2)]"/>
                              </div>
                              <div class="absolute flex top-1/3 left-10  sm:bottom-8 sm:-left-4 md:-left-5 md:bottom-8 xl:bottom-14 scale-50 sm:scale-95 md:scale-100">
                                  <MockOfficialSolution class="h-[250px] w-[400px]" />
                              </div>
                          </div>
                      </div>
                      <!-- wrong input -->
                      <div class=" items-center justify-center relative flex shake" id="feedback-section-wrong" v-if="showWrongInput">
                          <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="">
                          <div class="absolute flex md:items-center md:justify-center w-[350px] h-[400px] sm:h-[500px] sm:w-[600px] md:h-[500px] md:w-[700px] lg:scale-[0.9] xl:scale-100 xl:h-[500px] xl:w-[800px]">
                              <MockEditor />
                          </div>
                          <div class="absolute flex items-center md:ml-28  scale-75 sm:scale-100 ">
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
  </div>
</template>