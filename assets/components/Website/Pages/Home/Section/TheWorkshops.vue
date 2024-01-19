<script setup>
import PageSection from "../../PageSection.vue";
import Button from "../../../PrimaryButton.vue";
</script>

<template>
  <PageSection>
    <template #title>The Workshops</template>

    <div class="flex flex-wrap py-4">
      <div class="mb-8 w-full px-4 lg:w-1/2">
        <img src="../../../../../img/cloud/core-workshops.png" alt="" class="mb-4 h-56 w-auto" />
        <h4 class="mb-4 mt-0 p-0 font-work-sans text-lg font-bold capitalize not-italic text-white">core</h4>
        <p class="mb-4 text-sm text-white">
          Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni
          dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est.
        </p>
        <div class="flex justify-start">
          <Button href="/cloud" class="text-sm">Learn More</Button>
        </div>
      </div>

      <div class="mb-8 w-full px-4 lg:w-1/2">
        <img src="../../../../../img/cloud/community-workshops.png" alt="" class="mb-4 h-56 w-auto" />
        <h4 class="mb-4 mt-0 p-0 font-work-sans text-lg font-bold capitalize not-italic text-white">Community</h4>
        <p class="mb-4 text-sm text-white">
          Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni
          dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est.
        </p>
        <div class="flex justify-start">
          <Button href="/cloud" class="text-sm">Learn More</Button>
        </div>
      </div>
    </div>
  </PageSection>
</template>
