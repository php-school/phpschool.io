<script setup>
import {offset, autoPlacement} from "@floating-ui/dom";
import waitUntil from "./Utils/waitUntil.js";
import {onMounted, ref} from "vue";
import Shepherd from "shepherd.js";

const props = defineProps({
  student: Object,
  solutionFile: Object,
  firstRunLoaded: {
    type: Boolean,
    default: false,
  },
  firstVerifyLoaded: {
    type: Boolean,
    default: false,
  },
  problemModalOpen: Boolean,
});

const emit = defineEmits(['tour-starting']);

const forceTour = () => {
  createTour();
  tour.value.start();
}

defineExpose({
  forceTour,
})

const tour = ref(null);
const container = ref(null);

onMounted(() => {
  if (props.student.tour_complete === true) {
    return;
  }

  createTour();
  tour.value.start();
});

const createTour = () => {
  tour.value = new Shepherd.Tour({
    stepsContainer: container.value,
    useModalOverlay: true,
    keyboardNavigation: false,
    defaultStepOptions: {
      canClickTarget: false,
      classes: 'rounded-lg shadow-lg p-4 bg-gradient-to-r from-pink-600 to-purple-500 right-4 text-center text-white max-w-xs leading-6 border-4 border-solid border-purple-600 font-mono text-xs absolute top-[20px] z-[9998]',
      scrollTo: true,
      floatingUIOptions: {
        middleware: [offset({ mainAxis: 10,  })]
      },
      cancelIcon: {
        enabled: true,
      },
      when: {
        show() {
          const currentStepElement = tour.value.getCurrentStep().el;
          const content = currentStepElement.querySelector('.shepherd-content');
          const percentage = (100 / tour.value.steps.length) * (tour.value.steps.indexOf(tour.value.getCurrentStep()) + 1);

          content.insertAdjacentHTML(
              'beforeend',
              `<div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700 mt-4">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: ${percentage}%;"></div>
                  </div>`
          );
        }
      }
    },
  });

  tour.value.addStep({
    classes: 'max-w-sm',
    text: '<p class="text-md">👋 <span class="font-bold">Fancy a tour?</span> We\'ll show you how it all works, if not just hit cancel and crack on!</p>',
    buttons: [
      {
        text: 'No way!',
        action: function (e) {
          tour.value.cancel();
          e.stopPropagation();
        }
      },
      {
        text: 'Show me!',
        action: function(e) {
          e.stopPropagation();
          tour.value.next();
        }
      }
    ],
  });

  const problemModalIsOpen = function () {
    return props.problemModalOpen === true;
  };

  tour.value.addStep({
    attachTo: {
      element: '#problem-modal',
      on: 'right-start'
    },
    beforeShowPromise: function () {
      emit('tour-starting');
      return waitUntil(problemModalIsOpen)
    },
    text: 'For each exercise, there will be a problem you need to solve. It will open automatically when you enter the editor.',
    buttons: [
      {
        text: 'Next',
        action: function(e) {
          e.stopPropagation();
          tour.value.next();
        }
      }
    ],
  });

  tour.value.addStep({
    canClickTarget: true,
    attachTo: { element: '#problem-modal', on: 'right-end' },
    text: 'Read the problem and click Let\'s go!',
    advanceOn: {selector: '#lets-go', event: 'click'},
  });

  let timerId = null;

  tour.value.addStep({
    canClickTarget: true,
    attachTo: { element: '#editor-1', on: 'left-start' },
    text: '<p class="error bg-red-500 text-white p-3 text-base inline rounded-lg font-bold hidden absolute-center top-6"> Try again!<br></p>Write some code to solve the problem. Try adding <code>echo "Hey!";</code>. Then click next.',
    buttons: [
      {
        text: 'Next',
        action: function (e) {
          const step = tour.value.getCurrentStep();
          const el = step.getElement();
          const err = step.getElement().querySelector('.error');
          err.classList.add("hidden");

          if (timerId) {
            clearInterval(timerId);
          }

          if (!props.solutionFile.content.includes('echo "Hey!";')) {

            err.classList.remove('hidden');
            el.classList.add('shake');

            timerId = setTimeout(function () {
              el.classList.remove("shake");
              err.classList.add("hidden");
            }, 1000);
            return;
          }

          tour.value.next();
        }
      }
    ],
  });

  tour.value.addStep({
    canClickTarget: true,
    attachTo: { element: '#run', on: 'top' },
    text: 'Click run to run your solution and see the output',
    advanceOn: {selector: '#run', event: 'click'},
  });

  const isFirstRunLoaded = function () {
    return props.firstRunLoaded === true;
  };

  tour.value.addStep({
    attachTo: {
      element: '#run-modal',
      on: 'right'
    },
    text: 'Your program output is displayed here. You can execute your program multiple times.',
    beforeShowPromise: () => waitUntil(isFirstRunLoaded),
    buttons: [
      {
        text: 'Next',
        action: function(e) {
          e.stopPropagation();
          tour.value.next();
        }
      }
    ],
  });

  tour.value.addStep({
    canClickTarget: true,
    attachTo: { element: '#run-modal-close', on: 'right-start' },
    text: 'Close the run dialog to proceed.',
    advanceOn: {selector: '#run-modal-close', event: 'click'},
  });

  tour.value.addStep({
    canClickTarget: true,
    attachTo: { element: '#verify', on: 'top'},
    text: '🤞 Click verify to check if your solution fulfills the exercise requirements.',
    advanceOn: {selector: '#verify', event: 'click'},
  });

  const isFirstVerifyLoaded = function () {
    return props.firstVerifyLoaded === true;
  };

  tour.value.addStep({
    attachTo: {
      element: '#results-col',
      on: 'right-start'
    },
    text: 'The results of verifying your program are displayed here. ❌ It appears your program\'s output did not match the expected output.',
    beforeShowPromise: () => waitUntil(isFirstVerifyLoaded),
    buttons: [
      {
        text: 'Next',
        action: function(e) {
          e.stopPropagation();
          tour.value.next();
        }
      }
    ],
  });

  tour.value.addStep({
    text: '<p class="text-sm">🧐 <strong>Whoops!</strong> This doesn\'t seem to solve the problem. Can you fix it?</p>',
    buttons: [
      {
        text: 'Let\'s go!',
        action: tour.value.complete
      }
    ],
  });

  ['close', 'cancel',  'complete'].forEach(event => tour.value.on(event, () => {

    const opts = {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
    };

    fetch('/online/tour/complete', opts)
        .then(response => {
          if (!response.ok) {
            console.log('Could not set tour complete');
          }
        })
        .catch(error => {
          console.log('Could not set tour complete');
        })
  }));
}
</script>

<template>
  <div id="tour" ref="container"></div>
</template>

<style>

.shepherd-element > .shepherd-arrow {
  @apply absolute h-[20px] w-[20px] border-[10px] border-solid border-transparent;
}

.shepherd-element[data-popper-placement^=top] > .shepherd-arrow {
  @apply border-t-purple-600 bottom-[-23px];
}

.shepherd-element[data-popper-placement^=bottom] > .shepherd-arrow {
  @apply border-b-purple-600 top-[-23px];
}

.shepherd-element[data-popper-placement^=left] > .shepherd-arrow {
  @apply border-l-purple-600 right-[-23px];
}

.shepherd-element[data-popper-placement^=right] > .shepherd-arrow {
  @apply border-r-purple-600 left-[-23px];
}

.shepherd-element.shepherd-centered > .shepherd-arrow {
  @apply opacity-0;
}

.shepherd-content .shepherd-footer {
  @apply mt-4 mb-4 flex justify-center space-x-3;
}

.shepherd-content .shepherd-button {
  @apply inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none sm:w-auto sm:text-sm
}

.shepherd-modal-overlay-container {
  @apply h-0 left-0 opacity-0 overflow-hidden pointer-events-none fixed top-0 w-screen z-[9997];
  transition: all .3s ease-out, height 0ms .3s, opacity .3s 0ms;
}

.shepherd-modal-overlay-container.shepherd-modal-is-visible {
  @apply h-screen opacity-50;
  transform: translateZ(0);
  transition: all .3s ease-out, height 0s 0s, opacity .3s 0s
}

.shepherd-modal-overlay-container.shepherd-modal-is-visible path {
  @apply pointer-events-auto;
}

.shepherd-target-click-disabled.shepherd-enabled.shepherd-target,
.shepherd-target-click-disabled.shepherd-enabled.shepherd-target * {
  @apply pointer-events-none;
}
.shepherd-content .shepherd-cancel-icon {
  @apply absolute right-[7px] top-[1px];
}

.absolute-center {
  @apply absolute left-1/2 -translate-x-1/2;
}
</style>