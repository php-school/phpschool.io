<script setup>
import ContentHeader from "../../ContentHeader.vue";
</script>
<template>
    <p>So.. you want to create your own workshop? You want to teach a subject using the workshop environment? You want to make a meetup more hands on? You want to showcase your tool? You've come to the right place!</p>
    <p>We will be building a simple workshop which is available <a href="https://github.com/php-school/simple-math">here on GitHub</a> as a reference. So if you have any problems, you can double check your work.</p>

    <ContentHeader id="tutorial-sections">Tutorial Sections</ContentHeader>

    <h3><router-link to="/docs/tutorial/creating-your-own-workshop">1. Creating your own workshop</router-link></h3>
    <p>Learn how to create and setup your very own workshop.</p>
    <h3><router-link to="/docs/tutorial/modify-theme">2. Modifying the theme of your workshop</router-link></h3>
    <p>Customise the look and feel of your workshop, personalise to your brand or subject.</p>
    <h3><router-link to="/docs/tutorial/creating-an-exercise">3. Creating an exercise</router-link></h3>
    <p>Create the first exercise for your workshop.</p>

</template>
