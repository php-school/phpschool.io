<script setup>
import PrimaryButton from "./PrimaryButton.vue";
import { ChatBubbleLeftRightIcon } from "@heroicons/vue/24/solid";
import Modal from "../Online/ModalDialog.vue";
import { useStudentStore } from "../../stores/student";
import { ref } from "vue";

const studentStore = useStudentStore();

const inviteSent = ref(false);
const inviteError = ref(null);
const email = ref(studentStore.student ? studentStore.student.email : "");

const { open } = defineProps({
  open: Boolean,
});

const emit = defineEmits(["close"]);

const sendInvite = async () => {
  const response = await fetch("/api/slack-invite", {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      email: email.value,
    }),
  });

  inviteSent.value = true;
  if (!response.ok) {
    const data = await response.json();
    inviteError.value = data.error;
  }
};
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-100 ease-in"
      leave-active-class="transition-opacity duration-200 ease-in"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <Modal :scroll-content="true" size="md" max-height="max-h-[calc(5/6*100%)]" v-if="open" @close="emit('close')">
        <template #header>
          <div class="flex items-center">
            <ChatBubbleLeftRightIcon class="mr-2 h-6 w-6 text-pink-500" />
            <h3 class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-xl">Community Slack</h3>
          </div>
        </template>
        <template #body>
          <p class="text-white">
            You can find our community slack
            <a target="_blank" class="text-[#e91e63] hover:underline" href="https://phpschool-team.slack.com/archives/C0CGDLNFL">here</a>
            . If you don't have an account, use the form below with your e-mail to get an invite.
          </p>

          <div v-if="!inviteSent" class="relative flex w-full items-center pb-2 pt-6">
            <input
              type="email"
              placeholder="Type Something..."
              v-model="email"
              class="w-full rounded-2xl border-0 p-5 font-work-sans text-base font-bold text-gray-900 focus:border-pink-500 focus:outline-none focus:ring focus:ring-pink-500"
            />
            <PrimaryButton
              @click.stop="sendInvite"
              class="absolute right-0 m-0 flex h-[52px] items-center justify-center rounded-xl bg-gradient-to-r from-pink-600 to-purple-500 px-2 text-sm normal-case text-white shadow-none transition-all duration-300 ease-in hover:bg-[#aa1145] hover:opacity-90"
            >
              <span>Request Invite</span>
            </PrimaryButton>
          </div>
          <p v-if="inviteSent && inviteError === null" class="mt-4 w-full text-center text-base text-[#e91e63]"><i>Invite was sent, please check your e-mails.</i></p>
          <p v-if="inviteSent && inviteError" class="mt-4 w-full text-center text-base text-red-600">{{ inviteError }}</p>
        </template>
      </Modal>
    </Transition>
  </Teleport>
</template>
