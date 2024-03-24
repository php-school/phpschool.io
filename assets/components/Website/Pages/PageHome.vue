<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import PrimaryButton from "../PrimaryButton.vue";
import GitHubIcon from "../../Icons/GitHubIcon.vue";
import MockWorkshopExerciseList from "./Home/Mocks/MockWorkshopExerciseList.vue";
import MockWorkshopList from "./Home/Mocks/MockWorkshopList.vue";
import MockEditor from "./Home/Mocks/MockEditor.vue";
import MockProblemModal from "./Home/Mocks/MockProblemModal.vue";
import MockResults from "./Home/Mocks/MockResults.vue";
import MockPassNotification from "./Home/Mocks/MockPassNotification.vue";
import MockOfficialSolution from "./Home/Mocks/MockOfficialSolution.vue";
import MockProgress from "./Home/Mocks/MockProgress.vue";
import HomeHero from "./Home/HeroSection.vue";
import InfoSection from "./Home/InfoSection.vue";
import GettingStarted from "./Home/Section/GettingStarted.vue";
import TheWorkshops from "./Home/Section/TheWorkshops.vue";
import BuildYourOwn from "./Home/Section/BuildYourOwn.vue";

import { SparklesIcon } from "@heroicons/vue/24/solid";
import { useStudentStore } from "../../../stores/student";

const studentStore = useStudentStore();

const userInput = ref("");
const showInitialSetup = ref(true);
const showCorrectInput = ref(false);
const showWrongInput = ref(false);
const inputWrong = ref(false);

const userInputKeyup = (event) => {
  if (event.code === "Enter") {
    return checkInput();
  }

  showInitialSetup.value = true;
  showCorrectInput.value = false;
  showWrongInput.value = false;
  inputWrong.value = false;
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
    inputWrong.value = true;

    setTimeout(() => {
      inputWrong.value = false;
    }, 2000);
  }
};

const transitions = {
  exerciseList: ref(),
  workshopList: ref(),
  editor: ref(),
  editorWorkshopList: ref(),
  mockProblemModal: ref(),
  mockProgress: ref(),
  feedbackEditor: ref(),
  feedbackProblemModal: ref(),
};

const debounce = function (func, wait, immediate) {
  let timeout;
  return function () {
    const context = this,
      args = arguments;
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

    if (!container) {
      console.log(key);
    }

    const rect = container.getBoundingClientRect();
    const windowHeight = window.innerHeight;

    if (rect.top < windowHeight * 0.8) {
      container.classList.remove("opacity-0", "translate-y-6", "translate-x-10");
      container.classList.add("opacity-1");
    }
  });
}

const debouncedCheckImages = debounce(checkImages, 500);

onMounted(() => {
  window.addEventListener("scroll", checkImages);
  window.addEventListener("resize", debouncedCheckImages);
  checkImages();
});

onUnmounted(() => {
  window.removeEventListener("scroll", checkImages);
  window.removeEventListener("resize", debouncedCheckImages);
});
</script>

<template>
  <div>
    <header class="flex w-full flex-col">
      <HomeHero />
    </header>

    <!-- Information Section -->
    <section class="items-stretch overflow-hidden bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed py-20 sm:py-40">
      <!-- Section 1 -->
      <InfoSection>
        <template #left>
          <div class="mx-auto w-full lg:w-2/3">
            <div class="relative flex items-center justify-center">
              <img class="cover" src="../../../img/cloud/pattern-bg-square.svg" alt="" />
              <div class="absolute mx-auto scale-75 sm:scale-100 md:left-auto">
                <div :ref="transitions.exerciseList" class="fadeIn translate-y-6 opacity-0 transition-all duration-[1000ms] ease-in lg:mb-36">
                  <MockWorkshopExerciseList />
                </div>
              </div>
              <div class="absolute -right-4 top-1/4 scale-75 sm:scale-95 md:left-4 md:top-[80] md:scale-100 lg:top-[30] xl:top-[90]">
                <div :ref="transitions.workshopList" class="fadeIn translate-x-10 opacity-0 transition-all duration-[1000ms] ease-in">
                  <MockWorkshopList />
                </div>
              </div>
              <div class="absolute -bottom-36 right-10 z-50 hidden sm:block lg:-bottom-60 lg:left-10 2xl:left-60">
                <img class="bobbing-animation h-auto w-auto" src="../../../img/cloud/php-elephant-bandana.png" alt="" srcset="" />
              </div>
            </div>
          </div>
        </template>

        <template #right>
          <div class="mt-28 w-full space-y-8 text-left sm:px-5 md:mt-36 lg:mt-0 lg:w-1/3">
            <h2 class="font-work-sans text-5xl font-bold text-white">Open Source educational PHP Workshops</h2>
            <p class="font-base font-work-sans text-lg text-white">
              PHP School is a set of workshops each designed to teach a specific topic, tool, technology. Some beginner, some advanced. Each workshop consists of multiple exercises where your task is
              to code a solution to solve a problem. All our workshops are open source and you can contribute to them with spelling & bug fixes, new exercises. You can even build and publish your own
              workshop.
            </p>
            <div class="flex justify-start">
              <PrimaryButton to="/online">GET STARTED</PrimaryButton>
            </div>
          </div>
        </template>
      </InfoSection>

      <!-- Section 2 -->
      <InfoSection>
        <template #left>
          <div class="order-2 mt-8 w-full space-y-8 text-left sm:px-5 lg:order-1 lg:mt-0 lg:w-1/3">
            <h2 class="balanced font-work-sans text-5xl font-bold text-white">Online Browser Based IDE</h2>
            <p class="font-base font-work-sans text-lg text-white">
              Login in with your GitHub account, select a workshop, an exercise then jump straight in to our web based text editor (IDE). No complicated setup, no need to install tools, dependencies
              and text editors. Just jump in and start coding.
            </p>
            <div class="flex justify-start">
              <PrimaryButton to="/online" class="flex items-center">
                <span v-if="studentStore.student">TO THE WORKSHOPS</span>
                <div v-else class="flex items-center">
                  <GitHubIcon class="mr-2 h-6 w-6" />
                  <span>Log In with github</span>
                </div>
              </PrimaryButton>
            </div>
          </div>
        </template>

        <template #right>
          <div class="order-1 mx-auto w-full lg:order-1 lg:w-2/3">
            <div class="relative flex items-center justify-center">
              <img class="cover sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="" />
              <div
                :ref="transitions.editor"
                class="fadeIn absolute flex h-[400px] w-[350px] translate-y-6 opacity-0 transition-all duration-[1000ms] ease-in sm:h-[500px] sm:w-[600px] md:h-[500px] md:w-[700px] md:items-center md:justify-center lg:scale-[0.9] xl:h-[500px] xl:w-[800px] xl:scale-100"
              >
                <MockEditor class="overflow-hidden" />
              </div>
              <div :ref="transitions.editorWorkshopList" class="absolute -right-9 top-1/3 scale-75 sm:right-2 sm:top-1/2 md:top-1/3 xl:scale-100">
                <div class="fadeIn translate-x-10 opacity-0 transition-all duration-[1000ms] ease-in">
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
          <div class="relative mx-auto mt-20 w-full lg:w-2/3">
            <div class="relative flex items-center justify-center">
              <img class="" src="../../../img/cloud/pattern-bg-square-alt.svg" alt="" />
              <div :ref="transitions.mockProblemModal" class="fadeIn absolute translate-y-6 opacity-0 transition-all duration-[1000ms] ease-in">
                <MockProblemModal />
              </div>
              <div class="absolute -right-9 top-1/3 hidden scale-75 sm:-right-2 sm:top-1/3 sm:flex sm:scale-100 md:top-1/3">
                <div :ref="transitions.mockProgress" class="fadeIn translate-x-10 opacity-0 transition-all duration-[1000ms] ease-in">
                  <MockProgress />
                </div>
              </div>
              <div class="absolute -bottom-36 -left-10 z-50 hidden sm:block lg:-bottom-44 lg:-left-5">
                <!-- <div class="ellipse bobbing-animation"></div> -->
                <img class="bobbing-animation h-auto w-auto" src="/img/cloud/elephant-swim.png" alt="" srcset="" />
              </div>
            </div>
          </div>
        </template>

        <template #right>
          <div class="mt-28 w-full space-y-8 text-left sm:mt-40 sm:px-5 lg:mb-40 lg:w-1/3">
            <h2 class="font-work-sans text-5xl font-bold text-white">Work on practical assignments</h2>
            <p class="font-base font-work-sans text-lg text-white">
              Level up your problem solving skills whilst tackling practical problems that you will be sure to encounter in your chosen career path as a software developer. Each exercise comes with a
              description, code samples to get you started, links to documentation and other resources to help you solve the problem.
            </p>
            <div class="flex justify-start">
              <PrimaryButton to="/online">GET STARTED</PrimaryButton>
            </div>
          </div>
        </template>
      </InfoSection>

      <!-- Section 4 -->
      <InfoSection :divider="false">
        <template #left>
          <div class="order-2 mt-8 w-full space-y-8 text-left sm:px-5 lg:order-1 lg:w-1/3">
            <h2 class="font-work-sans text-5xl font-bold text-white">Get instant feedback and track your progress</h2>
            <p class="font-base font-work-sans text-lg text-white">
              Test your solution at any time to get instant feedback. If you get it wrong, we’ll show you how and what went wrong versus what we expected. Maybe you just need a simple tweak, or to
              altogether reconsider your approach. When you solve an exercise, your profile is updated and the exercise is marked as completed. Before proceeding you have the opportunity to see our
              official solution so you can compare notes and optimise your own solution. Maybe your solution is better!
              <br />
              <br />
              Try writing:
              <code>echo "Hello World";</code>
              in the input below to see how we verify your solution and provide feedback.
            </p>
            <div class="flex justify-start">
              <div class="relative flex w-full items-center" :class="{ shake: inputWrong }">
                <input
                  type="text"
                  placeholder="Type Something..."
                  v-model="userInput"
                  class="w-full rounded-2xl border-0 p-5 font-work-sans text-base font-bold text-gray-900 focus:outline-none focus:ring focus:ring-pink-500"
                  :class="{ 'ring ring-red-500': inputWrong }"
                  @keyup="userInputKeyup"
                />

                <PrimaryButton
                  @click="checkInput"
                  class="absolute right-0 m-0 flex h-[52px] w-24 items-center justify-center rounded-xl bg-gradient-to-r from-pink-600 to-purple-500 px-2 text-sm normal-case text-white shadow-none transition-all duration-300 ease-in hover:bg-[#aa1145] hover:opacity-90"
                >
                  <span>Verify</span>
                  <SparklesIcon v-cloak class="ml-2 h-5 w-5" />
                </PrimaryButton>
              </div>
            </div>
          </div>
        </template>

        <template #right>
          <div class="order-1 mx-auto w-full lg:order-2 lg:mt-0 lg:w-2/3">
            <!-- Initial set up -->
            <div class="relative flex items-center justify-center" id="feedback-section-initial" v-show="showInitialSetup">
              <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="" />
              <div
                :ref="transitions.feedbackEditor"
                class="fadeIn absolute flex h-[400px] w-[350px] translate-y-6 opacity-0 transition-all duration-[1000ms] ease-in sm:h-[500px] sm:w-[600px] md:h-[500px] md:w-[700px] md:items-center md:justify-center lg:scale-[0.9] xl:h-[500px] xl:w-[800px] xl:scale-100"
              >
                <MockEditor />
              </div>
              <div :ref="transitions.feedbackProblemModal" class="fadeIn absolute translate-x-10 scale-75 opacity-0 transition-all duration-[1000ms] ease-in">
                <MockProblemModal />
              </div>
            </div>

            <!-- correct input -->
            <div class="relative flex items-center justify-center" id="feedback-section-correct" v-show="showCorrectInput">
              <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="" />
              <div
                class="absolute flex h-[400px] w-[350px] sm:h-[500px] sm:w-[600px] md:h-[500px] md:w-[700px] md:items-center md:justify-center lg:scale-[0.9] xl:h-[500px] xl:w-[800px] xl:scale-100"
              >
                <MockEditor />
                <div class="absolute left-1/2 top-5 -translate-x-1/2 transform md:top-10 md:w-full md:scale-105 lg:top-8 xl:top-12 xl:px-4">
                  <MockPassNotification class="w-full shadow-[-10px_10px_0_rgba(0,0,0,0.2)]" />
                </div>
                <div class="absolute left-10 top-1/3 flex scale-50 sm:-left-4 sm:bottom-8 sm:scale-95 md:-left-5 md:bottom-8 md:scale-100 xl:bottom-14">
                  <MockOfficialSolution class="h-[250px] w-[400px]" />
                </div>
              </div>
            </div>
            <!-- wrong input -->
            <div class="shake relative flex items-center justify-center" id="feedback-section-wrong" v-show="showWrongInput">
              <img class="cover w-2/3 sm:h-auto sm:w-auto" src="../../../img/cloud/pattern-bg-rectangle.svg" alt="" />
              <div
                class="absolute flex h-[400px] w-[350px] sm:h-[500px] sm:w-[600px] md:h-[500px] md:w-[700px] md:items-center md:justify-center lg:scale-[0.9] xl:h-[500px] xl:w-[800px] xl:scale-100"
              >
                <MockEditor />
              </div>
              <div class="absolute flex scale-75 items-center sm:scale-100 md:ml-28">
                <MockResults />
              </div>
            </div>
          </div>
        </template>
      </InfoSection>
    </section>
    <GettingStarted />
    <TheWorkshops :logged-in="studentStore.student !== null" />
    <BuildYourOwn />
  </div>
</template>
