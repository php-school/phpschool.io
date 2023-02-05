<script>
import {offset} from "@floating-ui/dom";

export default {
  props: {
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
  },

  data() {
    return {
      tour: null,
    }
  },

  computed: {
  },

  created() {
  },

  mounted() {
    if (this.student.tour_complete === true) {
      return;
    }

    this.createTour();
    this.tour.start();
  },

  methods: {
    createTour() {
      this.tour = this.$shepherd({
        stepsContainer: this.$refs.tour,
        useModalOverlay: true,
        defaultStepOptions: {
          canClickTarget: false,
          classes: 'rounded-lg shadow-lg p-5 bg-gradient-to-r from-pink-600 to-purple-500 right-4 text-center text-white max-w-xs leading-6 border-4 border-solid border-purple-600 font-mono text-xs absolute top-[20px] z-[9998]',
          scrollTo: true,
          floatingUIOptions: {
            middleware: [offset({ mainAxis: 10,  })]
          },
          cancelIcon: {
            enabled: true,
          },
          when: {
            show() {

              const currentStepElement = this.tour.getCurrentStep().el;
              const content = currentStepElement.querySelector('.shepherd-content');
              const percentage = (100 / this.tour.steps.length) * (this.tour.steps.indexOf(this.tour.getCurrentStep()) + 1);

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

      this.tour.addStep({
        classes: 'max-w-sm',
        text: '<p class="text-md">👋 <span class="font-bold">Fancy a tour?</span> We\'ll show you how it all works, if not just hit cancel and crack on!</p>',
        buttons: [
          {
            text: 'No way!',
            action: function (e) {
              this.cancel();
              e.stopPropagation();
            }
          },
          {
            text: 'Show me!',
            action: function(e) {
              e.stopPropagation();
              this.next();
            }
          }
        ],
      });

      this.tour.addStep({
        attachTo: { element: '#problem-modal', on: 'right-start' },
        text: 'For each exercise, there will be a problem you need to solve. It will open automatically when you enter the editor.',
        buttons: [
          {
            text: 'Next',
            action: function(e) {
              e.stopPropagation();
              this.next();
            }
          }
        ],
      });

      this.tour.addStep({
        canClickTarget: true,
        attachTo: { element: '#problem-modal', on: 'right-end' },
        text: 'Read the problem and click Let\'s go!',
        advanceOn: {selector: '#lets-go', event: 'click'},
      });

      const file = this.solutionFile;
      let timerId = null;

      this.tour.addStep({
        canClickTarget: true,
        attachTo: { element: '#editor-1', on: 'left-start' },
        text: '<p class="error bg-red-500 text-white p-3 text-base inline rounded-lg font-bold hidden absolute-center top-6"> Try again!<br></p>Write some code to solve the problem. Try adding <code>echo "Hey!";</code>. Then click next.',
        buttons: [
          {
            text: 'Next',
            action: function (e) {
              const step = this.getCurrentStep();
              const el = step.getElement();
              const err = step.getElement().querySelector('.error');
              err.classList.add("hidden");

              if (timerId) {
                clearInterval(timerId);
              }

              if (!file.content.includes('echo "Hey!";')) {

                err.classList.remove('hidden');
                el.classList.add('shake');

                timerId = setTimeout(function () {
                  el.classList.remove("shake");
                  err.classList.add("hidden");
                }, 1000);
                return;
              }

              this.next();
            }
          }
        ],
      });

      this.tour.addStep({
        canClickTarget: true,
        attachTo: { element: '#run', on: 'top' },
        text: 'Click run to run your solution and see the output',
        advanceOn: {selector: '#run', event: 'click'},
      });

      const waitUntil = (condition, checkInterval = 10000) => {
        return new Promise(resolve => {
          let interval = setInterval(() => {
            if (!condition()) {
              return;
            }
            clearInterval(interval);
            resolve();
          }, checkInterval)
        })
      }

      const component = this;
      const isFirstRunLoaded = function () {
        return component.firstRunLoaded === true;
      };

      this.tour.addStep({
        attachTo: {
          element: '#run-modal',
          on: 'right'
        },
        text: 'Your program output is displayed here. You can execute your program multiple times.',
        beforeShowPromise: function () {
          return new Promise(resolve => {
            let interval = setInterval(() => {
              if (!isFirstRunLoaded()) {
                return;
              }
              clearInterval(interval);
              resolve();
            }, 500)
          })
        },
        buttons: [
          {
            text: 'Next',
            action: function(e) {
              e.stopPropagation();
              this.next();
            }
          }
        ],
      });

      this.tour.addStep({
        canClickTarget: true,
        attachTo: { element: '#run-modal-close', on: 'right-start' },
        text: 'Close the run dialog to proceed.',
        advanceOn: {selector: '#run-modal-close', event: 'click'},
      });

      this.tour.addStep({
        canClickTarget: true,
        attachTo: { element: '#verify', on: 'top' },
        text: '🤞 Click verify to check if your solution fulfills the exercise requirements.',
        advanceOn: {selector: '#verify', event: 'click'},
      });

      const isFirstVerifyLoaded = function () {
        return component.firstVerifyLoaded === true;
      };

      this.tour.addStep({
        attachTo: {
          element: '#results-col',
          on: 'right-start'
        },
        text: 'The results of verifying your program are displayed here. ❌ It appears your program\'s output did not match the expected output.',
        beforeShowPromise: function () {
          return new Promise(resolve => {
            let interval = setInterval(() => {
              if (!isFirstVerifyLoaded()) {
                return;
              }
              clearInterval(interval);
              resolve();
            }, 500)
          })
        },
        buttons: [
          {
            text: 'Next',
            action: function(e) {
              e.stopPropagation();
              this.next();
            }
          }
        ],
      });

      this.tour.addStep({
        text: '<p class="text-sm">🧐 <strong>Whoops!</strong> This doesn\'t seem to solve the problem. Can you fix it?</p>',
        buttons: [
          {
            text: 'Let\'s go!',
            action: this.tour.complete
          }
        ],
      });

      ['close', 'cancel',  'complete'].forEach(event => this.tour.on(event, () => {
        //let's set this as done

        const url = '/cloud/tour/complete';

        const opts = {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
        };

        fetch(url, opts)
            .then(response => {
              if (!response.ok) {
                console.log('Could not set tour complete');
              }
            })
            .catch(error => {
              console.log('Could not set tour complete');
            })
      }));
    },
  }
}
</script>


<template>
  <div id="tour" ref="tour"></div>
</template>

<style>

.shepherd-element > .shepherd-arrow {
  position: absolute;
  height: 20px;
  width: 20px;
}

.shepherd-element[data-popper-placement^=top] > .shepherd-arrow {
  @apply border-[10px] border-solid border-transparent border-t-purple-600;
  bottom: -23px;
}

.shepherd-element[data-popper-placement^=bottom] > .shepherd-arrow {
  @apply border-[10px] border-solid border-transparent border-b-purple-600;
  top: -23px;
}

.shepherd-element[data-popper-placement^=left] > .shepherd-arrow {
  @apply border-[10px] border-solid border-transparent border-l-purple-600;
  right: -23px;

}

.shepherd-element[data-popper-placement^=right] > .shepherd-arrow {
  @apply border-[10px] border-solid border-transparent border-r-purple-600;
  left: -23px;
}

.shepherd-element.shepherd-centered > .shepherd-arrow {
  opacity: 0
}

.shepherd-content .shepherd-footer {
  @apply mt-4 mb-4 flex justify-center space-x-3;
}

.shepherd-content .shepherd-button {
  @apply inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none sm:w-auto sm:text-sm
}

.shepherd-modal-overlay-container{height:0;left:0;opacity:0;overflow:hidden;pointer-events:none;position:fixed;top:0;transition:all .3s ease-out,height 0ms .3s,opacity .3s 0ms;width:100vw;z-index:9997}.shepherd-modal-overlay-container.shepherd-modal-is-visible{height:100vh;opacity:.5;transform:translateZ(0);transition:all .3s ease-out,height 0s 0s,opacity .3s 0s}.shepherd-modal-overlay-container.shepherd-modal-is-visible path{pointer-events:all}

.shepherd-target-click-disabled.shepherd-enabled.shepherd-target,
.shepherd-target-click-disabled.shepherd-enabled.shepherd-target * {
  pointer-events: none;
}
.shepherd-content .shepherd-cancel-icon {
  position: absolute;
  right: 7px;
  top: 1px;
}

.absolute-center {
  position: absolute;
  left: 50%;

  transform: translate(-50%, 0);
}
</style>