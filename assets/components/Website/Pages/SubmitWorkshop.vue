<script setup>

import {computed, reactive, ref} from "vue";
import {ArrowPathIcon, CheckIcon, XMarkIcon} from "@heroicons/vue/24/solid";

const form = reactive({
    email: null,
    name: null,
    contact: null,
    'workshop-name': null,
    'github-url': null,
})

const errors = ref({})
const workshopErrors = ref({})
const submitted = ref(false);
const submitError = ref(false);
const loading = ref(false);

const hasErrors = ref(false);

const submit = async () => {
    if (submitted.value) {
        return;
    }

    loading.value = true;
    submitError.value = false;
    errors.value = {};
    workshopErrors.value = {}

    const formData = new URLSearchParams();
    formData.append('email', form.email);
    formData.append('name', form.name);
    formData.append('contact', form.contact);
    formData.append('workshop-name', form["workshop-name"]);
    formData.append('github-url', form["github-url"]);

    const response = await fetch('/submit', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData,
    });

    loading.value = false;

    if (response.ok) {
        const data = await response.json();
        data.success = true
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
}

</script>

<template>
    <div class="font-open-sans flex flex-wrap mx-auto max-w-4xl text-left text-gray-800 p-6 mb-8">
        <div class="w-full md:w-1/3 md:pt-8">
            <h2 class="font-mono text-[#e91e63] text-xl mb-2">Built a workshop?</h2>
            <p class=" text-sm text-[#333]">Through this form you can submit your workshop for approval, once approved your workshop will appear on the website and be installable by the workshop manager. To learn how to build a workshop, head over to the <a class="text-[#e91e63] hover:underline" href="/docs">docs</a> or jump on to <a class="text-[#e91e63] hover:underline" href="<?= $links['slack'] ?>">slack</a> for any help!</p>

            <h2 class="font-mono text-[#e91e63] text-xl pt-6 mb-2">What is the approval process?</h2>
            <p class="text-sm text-[#333]">We are just making sure the quality is high and there is not lots of duplicated content across the workshops. We want PHP School to lead the way in quality education material.</p>

        </div>
        <div class="w-full md:w-2/3 md:pl-10 pt-8">
            <form action="/submit" method="post" @submit.prevent="submit">
                <div class="mb-8">
                    <label class="block mb-3 font-bold text-sm" for="email">Your Email Address <span class="text-[#e91e63]">*</span></label>
                    <p class="text-xs text-[#aaa] mb-4">This is used just to send you a notification when your workshop is approved.</p>
                    <div class="">
                        <input v-model="form.email" name="email" id="email" class="h-[48px] border border-[#ccc] rounded-full w-full text-sm bg-[1em] bg-no-repeat bg-[size:1.5em] bg-[url('/img/svg/mail.svg')] pl-[3em] focus:outline-none focus:border-[#e91e63]" type="text" placeholder="myname@example.com" required="">
                        <ul v-if="errors.email" class="border-l-4 border-[#cc1717] text-xs text-[#cc1717] my-3 p-3">
                            <li v-for="err in errors.email" class="mb-3 last:mb-0">{{err}}</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block mb-3 font-bold text-sm" for="name">Your Name <span class="text-[#e91e63]">*</span> </label>
                    <p class="text-xs text-[#aaa] mb-4">The name of workshop creator, shown on the homepage for bragging rights!</p>
                    <div class="">
                        <input v-model="form.name" name="name" id="name" class="h-[48px] border border-[#ccc] rounded-full w-full text-sm bg-[1em] bg-no-repeat bg-[size:1.5em] bg-[url('/img/svg/woman.svg')] pl-[3em] focus:outline-none focus:border-[#e91e63]" type="text" placeholder="Jane Doe" required="">
                        <ul v-if="errors.name" class="border-l-4 border-[#cc1717] text-xs text-[#cc1717] my-3 p-3">
                            <li v-for="err in errors.name" class="mb-3 last:mb-0">{{err}}</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block mb-3 font-bold text-sm" for="contact">Your Contact</label>
                    <p class="text-xs text-[#aaa] mb-4">For example your Twitter or GitHub, it is shown with your name next to the workshop on the homepage.</p>
                    <div class="">
                        <input v-model="form.contact" name="contact" id="contact" class="h-[48px] border border-[#ccc] rounded-full w-full text-sm bg-[1em] bg-no-repeat bg-[size:1.5em] bg-[url('/img/svg/social-media.svg')] pl-[3em] focus:outline-none focus:border-[#e91e63]" type="text" placeholder="https://twitter.com/your-name">
                        <ul v-if="errors.contact" class="border-l-4 border-[#cc1717] text-xs text-[#cc1717] my-3 p-3">
                            <li v-for="err in errors.contact" class="mb-3 last:mb-0">{{err}}</li>
                        </ul>
                    </div>
                </div>
                <div class="mb-8">
                    <label class="block mb-3 font-bold text-sm" for="workshop-name">Workshop Name <span class="text-[#e91e63]">*</span></label>
                    <div class="">
                        <input v-model="form['workshop-name']" name="workshop-name" id="workshop-name" class="h-[48px] border border-[#ccc] rounded-full w-full text-sm bg-[1em] bg-no-repeat bg-[size:1.5em] bg-[url('/img/svg/pencil.svg')] pl-[3em] focus:outline-none focus:border-[#e91e63]" type="text" placeholder="Learn You PHP!" required="">
                        <ul v-if="errors['workshop-name']" class="border-l-4 border-[#cc1717] text-xs text-[#cc1717] my-3 p-3">
                            <li v-for="err in errors['workshop-name']" class="mb-3 last:mb-0">{{err}}</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block mb-3 font-bold text-sm" for="github-url">GitHub Repository URL <span class="text-[#e91e63]">*</span></label>
                    <div class="">
                        <input v-model="form['github-url']" name="github-url" id="github-url" class="h-[48px] border border-[#ccc] rounded-full w-full text-sm bg-[1em] bg-no-repeat bg-[size:1.5em] bg-[url('/img/svg/github-dk.svg')] pl-[3em] focus:outline-none focus:border-[#e91e63]" type="text" placeholder="https://github.com/php-school/learn-you-php" required="">
                        <ul v-if="errors['github-url']" class="border-l-4 border-[#cc1717] text-xs text-[#cc1717] my-3 p-3">
                            <li v-for="err in errors['github-url']" class="mb-3 last:mb-0">{{err}}</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <button class="h-[48px] w-48 flex justify-center items-center text-white font-bold text-base px-10 py-2 uppercase my-4 mr-2 rounded-full bg-pink-600 hover:bg-pink-500">
                        <ArrowPathIcon v-cloak v-show="loading" class="w-5 h-5 animate-spin"/>
                        <CheckIcon v-cloak v-show="submitted" class="w-5 h-5"/>
                        <XMarkIcon v-cloak v-show="hasErrors" class="w-5 h-5"/>
                        <span v-show="!loading && !submitted && !hasErrors">Submit</span>
                    </button>
                </div>

                <ul v-if="submitError" class="border-l-4 border-[#cc1717] text-xs text-[#cc1717] my-3 p-3">
                    <li>Sorry but something went wrong please try again</li>
                </ul>

                <ul v-if="Object.keys(workshopErrors).length > 0" class="border-l-4 border-[#cc1717] text-xs text-[#cc1717] my-3 p-3">
                    <template v-for="field in workshopErrors">
                        <li v-for="err in field" class="mb-3 last:mb-0">{{err}}</li>
                    </template>
                </ul>
                <ul v-if="submitted" class="border-l-4 border-[#72af2f] text-xs text-[#72af2f] my-3 p-3">
                    <li>Your workshop has been successfully submitted. Once it has been approved, it will be listed on the homepage and you will be able to install it via the workshop manager.</li>
                </ul>
            </form>
        </div>
    </div>
</template>