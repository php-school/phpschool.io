<script setup>
import GitHubIcon from "../Icons/GitHubIcon.vue";
import Logo from "./Logo.vue";

const currentYear = (new Date().getFullYear());
</script>
<template>
    <footer class="bg-cyan-600 text-white font-sans text-sm">
        <div class="max-w-[2048px] h-auto mx-auto px-4 sm:px-6 lg:px-8 py-16 flex flex-wrap justify-between items-center">
            <figure class="text-white mb-4 sm:mb-0 ">
                <a href="/">
                    <Logo />
                </a>
            </figure>

            <div class="w-full sm:w-auto flex flex-col sm:flex-row justify-between gap-5 md:gap-10 items-start justify-items-start">
                <div class="mb-4 sm:mb-0 flex-grow">
                    <h4 class="text-white font-bold font-work-sans text-lg capitalize not-italic mb-2">The School</h4>
                    <ul class="list-none">
                        <li><a href="#">PHP Cloud</a></li>
                        <li><a href="#">PHP Terminal</a></li>
                        <li><a href="#">The Docs</a></li>
                    </ul>
                </div>

                <div class="mb-4 sm:mb-0 ml-0 sm:ml-6 flex-grow">
                    <h4 class="text-white font-bold font-work-sans text-lg capitalize not-italic mb-2">The Community</h4>
                    <ul class="list-none">
                        <li><a href="#">Events</a></li>
                        <li><a href="#"> Blog</a></li>
                        <li><a href="#">Need Help?</a></li>
                    </ul>
                </div>

                <div class="mb-4 sm:mb-0 ml-0 sm:ml-6 flex-grow">
                    <h4 class="text-white font-bold font-work-sans text-lg capitalize not-italic mb-2">The Creators</h4>
                    <ul class="list-none">
                        <li><a href="#">About us</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">History</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex justify-center w-full max-w-[2048px] px-4 sm:px-6 lg:px-8 mx-auto">
            <hr class="border-t-0 border-white border-solid w-full">
        </div>

        <div class="max-w-[2048px] h-auto mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-wrap justify-between items-center">
            <p>&copy; Copyright {{currentYear}}</p>

            <div class="mb-4 sm:mb-0">
                <p>This Site is <a href="https://github.com/php-school/phpschool.io" class="text-pink-400">Open Source on GitHub</a> & we welcome corrections/improvements</p>
            </div>

            <div class="">
                <a href="https://github.com/php-school/phpschool.io">
                    <GitHubIcon class="h-10 w-10 hover:text-pink-400" />
                </a>
            </div>
        </div>
    </footer>
</template>
