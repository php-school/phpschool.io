<script setup>
import Note from "../DocNote.vue";
</script>

<template>
  <p>Welcome to the PHP School documentation - The documentation is spit up in to several sections:</p>

  <h3><router-link to="/docs/tutorial">Workshop Tutorial</router-link></h3>
  <p>Learn how to setup and build a workshop step by step!</p>

  <h3>
    <router-link to="/docs/reference">Reference Documentation</router-link>
  </h3>
  <p>
    Learn about the individual components of the workshop such as the available checks, exercise types, solutions,
    services and so on.
  </p>

  <br />
  <note>
    <p>
      Already built a workshop? Go forth and
      <router-link to="/submit">submit it!</router-link>
    </p>
  </note>
</template>
