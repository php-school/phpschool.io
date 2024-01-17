<script setup>
import PageSection from '../../PageSection.vue'
import Button from '../../../PrimaryButton.vue'
</script>

<template>
    <PageSection>
        <template #title>The Workshops</template>

        <div class="flex flex-wrap py-4">
            <div class="w-full lg:w-1/2 px-4 mb-8">
                <img
                    src="../../../../../img/cloud/core-workshops.png"
                    alt=""
                    class="h-56 w-auto mb-4"
                />
                <h4
                    class="text-lg font-work-sans font-bold capitalize text-white mt-0 p-0 not-italic mb-4"
                >
                    core
                </h4>
                <p class="text-white text-sm mb-4">
                    Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed
                    quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque
                    porro quisquam est.
                </p>
                <div class="flex justify-start">
                    <Button href="/cloud" class="text-sm">Learn More</Button>
                </div>
            </div>

            <div class="w-full lg:w-1/2 px-4 mb-8">
                <img
                    src="../../../../../img/cloud/community-workshops.png"
                    alt=""
                    class="h-56 w-auto mb-4"
                />
                <h4
                    class="text-lg font-work-sans font-bold capitalize text-white mt-0 p-0 not-italic mb-4"
                >
                    Community
                </h4>
                <p class="text-white text-sm mb-4">
                    Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed
                    quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque
                    porro quisquam est.
                </p>
                <div class="flex justify-start">
                    <Button href="/cloud" class="text-sm">Learn More</Button>
                </div>
            </div>
        </div>
    </PageSection>
</template>
