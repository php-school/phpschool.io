<script setup>
import { onMounted, reactive, ref } from "vue";
import { ArrowPathIcon, CheckIcon, XMarkIcon } from "@heroicons/vue/24/solid";
import Heading from "../PageHeading.vue";

const form = reactive({
  email: null,
  name: null,
  contact: null,
  "workshop-name": null,
  "github-url": null,
});

const errors = ref({});
const workshopErrors = ref({});
const submitted = ref(false);
const submitError = ref(false);
const loading = ref(false);

const hasErrors = ref(false);

onMounted(() => {
  form.value = {
    email: null,
    name: null,
    contact: null,
    "workshop-name": null,
    "github-url": null,
  };
});

const submit = async () => {
  if (submitted.value) {
    return;
  }

  loading.value = true;
  submitError.value = false;
  errors.value = {};
  workshopErrors.value = {};

  const response = await fetch("/api/submit", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(form),
  });

  loading.value = false;

  if (response.ok) {
    const data = await response.json();

    if (data.success === false) {
      if (data.form_errors) {
        errors.value = data.form_errors;
      }

      if (data.workshop_errors) {
        workshopErrors.value = data.workshop_errors;
      }

      hasErrors.value = true;

      setTimeout(() => {
        hasErrors.value = false;
      }, 5000);
    } else {
      submitted.value = true;
    }
  } else {
    submitError.value = true;
  }
};
</script>

<template>
  <div>
    <section class="items-stretch bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed">
      <Heading>
        <template v-slot:title>Submit your workshop</template>

        <template v-slot:description>PHP School is nothing without the community so it's awesome you are considering building and submitting a workshop.</template>
      </Heading>
    </section>
    <div class="w-full bg-gray-900">
      <div class="mx-auto flex max-w-4xl flex-wrap p-6 pb-20 text-left font-open-sans text-gray-300">
        <div class="w-full md:w-1/3 md:pt-8">
          <h2 class="mb-2 font-mono text-xl text-[#e91e63]">Built a workshop?</h2>
          <p class="text-sm">
            Through this form you can submit your workshop for approval, once approved your workshop will appear on the website and be installable by the workshop manager. To learn how to build a
            workshop, head over to the
            <a class="text-[#e91e63] hover:underline" href="/docs">docs</a>
            or jump on to
            <a class="text-[#e91e63] hover:underline" href="<?= $links['slack'] ?>">slack</a>
            for any help!
          </p>

          <h2 class="mb-2 pt-6 font-mono text-xl text-[#e91e63]">What is the approval process?</h2>
          <p class="text-sm">
            We are just making sure the quality is high and there is not lots of duplicated content across the workshops. We want PHP School to lead the way in quality education material.
          </p>
        </div>
        <div class="w-full pt-8 md:w-2/3 md:pl-10">
          <form action="/submit" method="post" @submit.prevent="submit">
            <div class="mb-8">
              <label class="mb-3 block text-sm font-bold" for="email">
                Your Email Address
                <span class="text-[#e91e63]">*</span>
              </label>
              <p class="mb-4 text-xs text-gray-300/70">This is used just to send you a notification when your workshop is approved.</p>
              <div class="">
                <input
                  v-model="form.email"
                  name="email"
                  id="email"
                  class="h-[48px] w-full rounded-full border border-[#ccc] bg-[url('/img/cloud/mail.svg')] bg-[size:1.5em] bg-[1em] bg-no-repeat pl-[3em] text-sm text-black focus:border-[#e91e63] focus:outline-none"
                  type="text"
                  placeholder="myname@example.com"
                  required=""
                />
                <ul v-if="errors.email" class="my-3 border-l-4 border-[#cc1717] p-3 text-xs text-[#cc1717]">
                  <li v-for="err in errors.email" :key="err" class="mb-3 last:mb-0">
                    {{ err }}
                  </li>
                </ul>
              </div>
            </div>

            <div class="mb-8">
              <label class="mb-3 block text-sm font-bold" for="name">
                Your Name
                <span class="text-[#e91e63]">*</span>
              </label>
              <p class="mb-4 text-xs text-gray-300/70">The name of workshop creator, shown on the homepage for bragging rights!</p>
              <div class="">
                <input
                  v-model="form.name"
                  name="name"
                  id="name"
                  class="h-[48px] w-full rounded-full border border-[#ccc] bg-[url('/img/cloud/woman.svg')] bg-[size:1.5em] bg-[1em] bg-no-repeat pl-[3em] text-sm text-black focus:border-[#e91e63] focus:outline-none"
                  type="text"
                  placeholder="Jane Doe"
                  required=""
                />
                <ul v-if="errors.name" class="my-3 border-l-4 border-[#cc1717] p-3 text-xs text-[#cc1717]">
                  <li v-for="err in errors.name" :key="err" class="mb-3 last:mb-0">
                    {{ err }}
                  </li>
                </ul>
              </div>
            </div>

            <div class="mb-8">
              <label class="mb-3 block text-sm font-bold" for="contact">Your Contact</label>
              <p class="mb-4 text-xs text-gray-300/70">For example your Twitter or GitHub, it is shown with your name next to the workshop on the homepage.</p>
              <div class="">
                <input
                  v-model="form.contact"
                  name="contact"
                  id="contact"
                  class="h-[48px] w-full rounded-full border border-[#ccc] bg-[url('/img/cloud/social-media.svg')] bg-[size:1.5em] bg-[1em] bg-no-repeat pl-[3em] text-sm text-black focus:border-[#e91e63] focus:outline-none"
                  type="text"
                  placeholder="https://twitter.com/your-name"
                />
                <ul v-if="errors.contact" class="my-3 border-l-4 border-[#cc1717] p-3 text-xs text-[#cc1717]">
                  <li v-for="err in errors.contact" :key="err" class="mb-3 last:mb-0">
                    {{ err }}
                  </li>
                </ul>
              </div>
            </div>
            <div class="mb-8">
              <label class="mb-3 block text-sm font-bold" for="workshop-name">
                Workshop Name
                <span class="text-[#e91e63]">*</span>
              </label>
              <div class="">
                <input
                  v-model="form['workshop-name']"
                  name="workshop-name"
                  id="workshop-name"
                  class="h-[48px] w-full rounded-full border border-[#ccc] bg-[url('/img/cloud/pencil.svg')] bg-[size:1.5em] bg-[1em] bg-no-repeat pl-[3em] text-sm text-black focus:border-[#e91e63] focus:outline-none"
                  type="text"
                  placeholder="Learn You PHP!"
                  required=""
                />
                <ul v-if="errors['workshop-name']" class="my-3 border-l-4 border-[#cc1717] p-3 text-xs text-[#cc1717]">
                  <li v-for="err in errors['workshop-name']" :key="err" class="mb-3 last:mb-0">
                    {{ err }}
                  </li>
                </ul>
              </div>
            </div>

            <div class="mb-8">
              <label class="mb-3 block text-sm font-bold" for="github-url">
                GitHub Repository URL
                <span class="text-[#e91e63]">*</span>
              </label>
              <div class="">
                <input
                  v-model="form['github-url']"
                  name="github-url"
                  id="github-url"
                  class="h-[48px] w-full rounded-full border border-[#ccc] bg-[url('/img/cloud/github-dk.svg')] bg-[size:1.5em] bg-[1em] bg-no-repeat pl-[3em] text-sm text-black focus:border-[#e91e63] focus:outline-none"
                  type="text"
                  placeholder="https://github.com/php-school/learn-you-php"
                  required=""
                />
                <ul v-if="errors['github-url']" class="my-3 border-l-4 border-[#cc1717] p-3 text-xs text-[#cc1717]">
                  <li v-for="err in errors['github-url']" :key="err" class="mb-3 last:mb-0">
                    {{ err }}
                  </li>
                </ul>
              </div>
            </div>

            <div>
              <button class="my-4 mr-2 flex h-[48px] w-48 items-center justify-center rounded-full bg-pink-600 px-10 py-2 text-base font-bold uppercase text-white hover:bg-pink-500">
                <ArrowPathIcon v-cloak v-show="loading" class="h-5 w-5 animate-spin" />
                <CheckIcon v-cloak v-show="submitted" class="h-5 w-5" />
                <XMarkIcon v-cloak v-show="hasErrors" class="h-5 w-5" />
                <span v-show="!loading && !submitted && !hasErrors">Submit</span>
              </button>
            </div>

            <ul v-if="submitError" class="my-3 border-l-4 border-[#cc1717] p-3 text-xs text-[#cc1717]">
              <li>Sorry but something went wrong please try again</li>
            </ul>

            <ul v-if="Object.keys(workshopErrors).length > 0" class="my-3 border-l-4 border-[#cc1717] p-3 text-xs text-[#cc1717]">
              <template v-for="(field, i) in workshopErrors" :key="i">
                <li v-for="err in field" :key="err" class="mb-3 last:mb-0">
                  {{ err }}
                </li>
              </template>
            </ul>
            <ul v-if="submitted" class="my-3 border-l-4 border-[#72af2f] p-3 text-xs text-[#72af2f]">
              <li>Your workshop has been successfully submitted. Once it has been approved, it will be listed on the homepage and you will be able to install it via the workshop manager.</li>
            </ul>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
