<script setup>
import Logo from "../../SiteLogo.vue";
import CodeAnimation from "../../CodeAnimation.vue";
</script>

<template>
  <div class="z-10 -mt-28 bg-[url('/img/cloud/phpCity-alt.jpg')] bg-cover bg-center">
    <div
      class="mx-auto flex h-screen w-full max-w-[2048px] items-center justify-center pt-28 text-center lg:justify-between"
    >
      <div class="code-fade hidden overflow-hidden px-4 text-white drop-shadow-[0_0_5px_rgba(255,255,255,1)] lg:block">
        <CodeAnimation class="text-left text-sm" />
      </div>
      <div>
        <figure class="transition-all duration-500 ease-in-out hover:drop-shadow-[0_0_10px_rgba(255,255,255,1)]">
          <Logo class="h-64 w-64 text-white hover:text-white" />
        </figure>
        <div class="mt-20">
          <a
            href=" /cloud"
            class="items-center rounded-full border-2 bg-none px-10 py-2 font-thin uppercase text-white transition-all duration-300 ease-in-out hover:bg-white hover:text-gray-800 hover:no-underline"
          >
            get started
          </a>
        </div>
      </div>
      <div class="code-fade hidden overflow-hidden px-4 text-white drop-shadow-[0_0_5px_rgba(255,255,255,1)] lg:block">
        <CodeAnimation class="text-right text-sm" />
      </div>
    </div>
  </div>
</template>

<style>
.code-fade {
  -webkit-mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
  mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
  height: 320px;
  width: 600px;
  overflow-y: hidden;
}
</style>
