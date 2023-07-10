<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
</script>

<template>
  <div class="flex flex-col w-full">
    <nav class="site-header w-full  py-2 px-6">
      <div class="max-w-[2048px] mx-auto flex justify-between items-center">
        <ul class="flex items-center gap-2 ">
          <li class="text-white p-2 uppercase">
            <figure>
              <a href="/" class="">
                <svg class="h-20 w-20" version="1.1" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                  x="0px" y="0px" width="171.3px" height="167.4px" viewBox="0 0 171.3 167.4"
                  enable-background="new 0 0 171.3 167.4" xml:space="preserve">
                  <defs></defs>
                  <g>
                    <path
                      d="M120.7,97.8c4.9,0,8.9-4.1,8.9-9.4c0-5.2-4-9.1-8.9-9.1c-4.9,0-8.9,3.9-8.9,9.1C111.8,93.7,115.7,97.8,120.7,97.8z" />
                    <path
                      d="M40.8,49.9c2,0,3.5-0.3,4.9-0.9c2.8-1.2,4.4-4,4.4-7.8c0-3.5-1.4-6.2-3.9-7.5c-1.4-0.7-3.2-1.1-5.3-1.1h-8.2v17.2h8.1V49.9z" />
                    <path
                      d="M134.2,49.9c2,0,3.5-0.3,4.9-0.9c2.8-1.2,4.4-4,4.4-7.8c0-3.5-1.4-6.2-3.9-7.5c-1.4-0.7-3.2-1.1-5.3-1.1H126v17.2h8.2V49.9z" />
                    <path d="M0,0v125.7l85.7,41.7l85.6-41.7V0H0z M167.5,122l-81.8,39.8l-0.4-0.2L3.8,122V4.7h163.7V122z" />
                    <path
                      d="M165.6,120.7V6.7H5.8v114l79.9,38.8L165.6,120.7z M92,127.1c-2.1,0-2.8-1.8-2.8-4c0-2.2,0.7-4,2.8-4s2.7,1.8,2.7,4C94.7,125.3,94,127.1,92,127.1z M100.4,126.9h-4.5V126h1.7v-5c0-0.2,0-0.5,0-0.5s-0.1,0.2-0.4,0.5l-0.7,0.7l-0.7-0.7l1.9-1.8h1v6.8h1.7V126.9z M103.5,127.2c-1.6,0-2.4-1.1-2.4-1.1l0.7-0.9c0,0,0.6,0.9,1.8,0.9c1,0,1.7-0.7,1.7-1.6c0-1-0.8-1.6-1.8-1.6c-0.9,0-1.5,0.4-1.5,0.4l-0.5-0.2l0.3-3.2c0-0.5,0.2-0.7,0.7-0.7h2.7c0.5,0,0.7,0.2,0.7,0.7v1h-1v-0.5c0-0.2-0.1-0.2-0.2-0.2H103c-0.2,0-0.2,0.1-0.2,0.2l-0.2,1.4c0,0.2-0.1,0.4-0.1,0.4s0.4-0.2,1-0.2c1.7,0,2.8,1.1,2.8,2.6C106.3,126,105.2,127.2,103.5,127.2z M114.3,66.6h5.5V32.3h-5.5v-4.8h20.5c2.7,0,5.3,0.2,7.5,1.2c4.5,1.9,7.5,6.6,7.5,12.4c0,6.2-3.2,11.1-8.3,12.9c-2.1,0.7-4.2,0.9-6.6,0.9H126v11.6h5.5v4.8h-17.2V66.6z M148.8,97.5v1.9h-12.2V77.6h2.1v19.9H148.8z M120.7,77.2c6.2,0,11.1,4.9,11.1,11.1c0,6.4-4.9,11.4-11.1,11.4s-11.1-5-11.1-11.4C109.6,82.1,114.5,77.2,120.7,77.2z M61.6,66.6h5.5V32.3h-5.5v-4.8h17.2v4.8h-5.5V47H96V32.3h-5.5v-4.8h17.2v4.8h-5.5v34.3h5.5v4.8H90.5v-4.8H96V52.1H73.3v14.5h5.5v4.8H61.6V66.6z M106.2,88.3c0,6.4-4.9,11.4-11.1,11.4c-6.2,0-11.1-5-11.1-11.4c0-6.2,5-11.1,11.1-11.1C101.3,77.2,106.2,82.1,106.2,88.3z M64.4,89.4v10h-2.1V77.6h2.1v9.9H77v-9.9h2.1v21.8H77v-10H64.4z M57,80l-1.1,1.6c0,0-2.5-2.4-6.6-2.4c-5.1,0-8.8,4-8.8,9.1c0,5.2,3.7,9.5,8.8,9.5c4.4,0,7.1-2.9,7.1-2.9l1.2,1.5c0,0-3,3.4-8.3,3.4c-6.4,0-11-5.1-11-11.4c-0.1-6.4,4.6-11.2,10.9-11.2C54.4,77.2,57,80,57,80z M21,66.6h5.5V32.3H21v-4.8h20.5c2.7,0,5.3,0.2,7.5,1.2c4.5,1.9,7.5,6.6,7.5,12.4c0,6.2-3.2,11.1-8.3,12.9c-2.1,0.8-4.2,1-6.6,1h-8.9v11.6h5.5v4.8H21V66.6z M21.8,83c0-3.1,2.8-5.9,6.8-5.9c3.8,0,5.8,2.1,5.8,2.1L33.3,81c0,0-1.9-1.9-4.8-1.9c-2.8,0-4.6,1.8-4.6,3.8c0,5.2,10.9,3.7,10.9,10.9c0,3.3-2.5,5.9-6.6,5.9c-4.5,0-6.9-2.8-6.9-2.8l1.4-1.6c0,0,2.3,2.4,5.7,2.4c2.4,0,4.3-1.4,4.3-3.8C32.7,88.4,21.8,89.8,21.8,83z M19.7,108.7h131v1h-131V108.7z M85.4,120.1c-0.7,0-1.2,0.4-1.2,0.7v0.3h-1v-0.7c0-1,1.4-1.4,2.3-1.4c1.4,0,2.4,0.9,2.4,2.2c0,2.7-3.9,2.7-3.9,4.5c0,0.2,0.1,0.2,0.3,0.2H87c0.2,0,0.2-0.1,0.2-0.2v-0.5h1v1c0,0.5-0.2,0.7-0.7,0.7h-4c-0.6,0-0.7-0.3-0.7-0.9c0-2.8,4-2.6,4-4.7C86.8,120.6,86.3,120.1,85.4,120.1z M70.3,126.3c0,0.5-0.2,0.7-0.7,0.7h-3.5c-0.5,0-0.7-0.2-0.7-0.7v-6.2h-0.7v-0.9h4.6c0.5,0,0.7,0.2,0.7,0.7v1h-1v-0.5c0-0.2-0.1-0.2-0.2-0.2h-2.2v2.4h2.7v0.9h-2.7v2.3c0,0.2,0.1,0.2,0.2,0.2h2.3c0.2,0,0.2-0.1,0.2-0.2v-0.5h1V126.3z M73.2,127c-1.5,0-2.2-1-2.2-1l0.4-0.6c0,0,0.7,0.8,1.7,0.8c0.5,0,1-0.2,1-0.7c0-1-3-0.9-3-2.7c0-1.1,0.9-1.6,2.1-1.6c0.7,0,1.8,0.2,1.8,1.1v0.5h-1v-0.3c0-0.3-0.5-0.5-0.8-0.5c-0.6,0-1,0.2-1,0.7c0,1.1,3,0.8,3,2.7C75.2,126.4,74.3,127,73.2,127z M79,127c0,0-0.1,0-0.4,0c-0.7,0-2.1-0.2-2.1-2.1v-2.7h-0.7v-0.9h0.7v-1.5h1v1.5h1.3v0.9h-1.3v2.6c0,1.1,0.8,1.3,1.2,1.3c0.2,0,0.3,0,0.3,0V127z M81.7,138.1l2.7-0.4l1.3-2.5l1.2,2.5l2.8,0.4l-2,1.9l0.4,2.8l-2.4-1.3l-2.5,1.3l0.5-2.8L81.7,138.1z" />
                    <path
                      d="M86.2,88.4c0.1,5.3,4,9.4,8.9,9.4c4.9,0,8.9-4.1,8.9-9.4c0-5.2-4-9.1-8.9-9.1C90.2,79.3,86.2,83.2,86.2,88.4z" />
                    <path
                      d="M92,120.1c-1.3,0-1.6,1.3-1.6,3c0,1.7,0.4,3,1.6,3s1.6-1.4,1.6-3C93.6,121.4,93.2,120.1,92,120.1z" />
                  </g>
                </svg>
              </a>
            </figure>
          </li>
          <li class="text-white text-xs p-2 uppercase hover:text-pink-500 cursor-pointer transition duration-150">Cloud
          </li>
          <li class="text-white text-xs p-2 uppercase hover:text-pink-500 cursor-pointer transition duration-150">Local
          </li>
          <li class="text-white text-xs p-2 uppercase hover:text-pink-500 cursor-pointer transition duration-150">Support
          </li>

          <!-- DROPDOWN START -->
          <Menu as="li" class="relative text-white text-xs p-2 uppercase" v-slot="{ open }">
            <div class="group">
              <MenuButton :class="open ? 'text-pink-500' : 'text-white'"
                class="inline-flex w-full justify-center items-center  uppercase text-xs group-hover:text-pink-500 cursor-pointer transition duration-200">
                For Developers
                <ChevronDownIcon :class="open ? 'rotate-180 text-pink-500' : ''"
                  class="ml-2 h-5 w-5 text-gray-400 transition duration-200 group-hover:text-pink-500"
                  aria-hidden="true" />
              </MenuButton>
            </div>

            <transition enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95">
              <MenuItems
                class="absolute text-left right-0 z-10 mt-4 w-56 origin-top-right rounded-md bg-gray-800 shadow-brand-shadow ring-1 ring-black ring-opacity-5 focus:outline-none">
                <div class="py-1">
                  <MenuItem v-slot="{ active }">
                  <a href="#"
                    :class="[active ? 'bg-gray-700 text-pink-500 ' : 'text-white', 'block px-4 py-4 text-xs hover:no-underline']">Build
                    a workshop</a>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                  <a href="#"
                    :class="[active ? 'bg-gray-700 text-pink-500' : 'text-white', 'block px-4 py-4 text-xs hover:no-underline']">Submit
                    your workshop</a>
                  </MenuItem>
                </div>
              </MenuItems>
            </transition>
          </Menu>


          <li class="text-white text-xs p-2 uppercase hover:text-pink-500 cursor-pointer transition duration-150">Events
          </li>
          <li class="text-white text-xs p-2 uppercase hover:text-pink-500 cursor-pointer transition duration-150">Blog
          </li>
        </ul>

        <ul class="flex gap-1">
          <li class="text-white text-xs px-8 py-4 uppercase my-4 mr-2 rounded-full bg-pink-600 hover:bg-pink-500">Log In
          </li>
          <li class="text-white text-xs px-8 py-4 uppercase my-4 mr-2 rounded-full bg-pink-600 hover:bg-pink-500">Register
          </li>
        </ul>
      </div>
    </nav>
    <section class="flex flex-col w-full animateGradientBackground">
      <div class="max-w-[2048px] mx-auto">
        <div class="w-full flex items-center justify-center flex-col  text-center pt-20 pb-10 gap-4 ">

          <p class="text-white text text-2xl font-sans font-normal">A revolutionary new way to learn PHP</p>
          <h1 class="text-white text-6xl font-mono uppercase font-bold tracking-wider">Open source learning for php</h1>
          <p class="text-white text text-2xl font-sans font-light">Bring your imagination to life in an open learning
            eco-system</p>
          <button
            class="text-white font-bold text-base  px-10 py-2 uppercase my-4 mr-2 rounded-full bg-pink-600 hover:bg-pink-500 shadow-brand-shadow">GET
            STARTED</button>

        </div>
        <figure class="w-full flex items-center justify-center flex-col  text-center pb-20">
          <img src="/cloud/img/editor-placeholder.png" alt=" placeholder">
        </figure>
      </div>
    </section>

    <section class="bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed items-stretch">

      <div
        class=" flex sm:flex-col text-center lg:flex-row max-w-[2048px] mx-auto p-4 mt-40 justify-center items-center   lg:gap-20 ">
        <div class="mx-auto relative w-full lg:w-2/3">
          <div class=" items-center justify-center flex ">
            <img class="cover w-auto " src="/cloud/img/pattern-bg-square.svg" alt="">
            <div class="absolute inset-0 flex items-center justify-center">
              <img class="" src="/cloud/img/workshops.svg" alt="workshops">
            </div>
            <div
              class="absolute right-10 lg:-right-16 xl:right-14   top-[90] md:top-[80] lg:top-[30] xl:top-[90] md:w-auto md:h-auto">
              <img class="" src=" /cloud/img/workshops-selector.svg" alt="workshop-selector">
            </div>
            <div class="absolute  -bottom-36 right-10   lg:-bottom-40 lg:left-64  ">
              <!-- <div class="ellipse bobbing-animation"></div> -->
              <img class="bobbing-animation" src="/cloud/img/php-elephant-bandana.svg" alt="" srcset="">
            </div>
          </div>
        </div>
        <div class="sm:w-full lg:w-1/3 space-y-8 mt-40 px-20 ">
          <h2 class="font-work-sans text-white font-bold text-4xl text-left ">Open Source PHP Workshops</h2>
          <p class="font-work-sans text-white font-base text-lg text-left">PHP School is a set of workshops each
            designed to teach a specific topic, tool, technology. Some beginner, some advanced. Each workshop consists of
            multiple exercises where your task is to code a solution to solve a problem. All our workshops are open
            source, you can contribute to them also, with bug fixes, new exercises, or your own workshop.</p>
          <div class="flex justify-start">
            <button class=" text-white font-bold text-base uppercase bg-pink-600 rounded-full py-2 px-10">Get
              Started</button>
          </div>
        </div>
      </div>


      <img class="w-full my-14" src="/cloud/img/php-cloud-border.svg" alt="">

      <!-- PLACEHOLDER CONTENT -->



    </section>
  </div>
</template>