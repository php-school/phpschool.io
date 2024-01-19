<script setup>
import Code from "../../DocCode.vue";
import ContentHeader from "../../ContentHeader.vue";
</script>
<template>
  <p>
    In this section you will learn about the internals of the workshop application which will enable you to build robust
    and advanced workshops.
  </p>
  <p>You can use this section as a useful reference to refer back to during the development of your workshop.</p>
  <p>
    For a walk through approach detailing the initial setup of a workshop and the development of your first exercise,
    check out the
    <router-link to="/docs/tutorial">Workshop Tutorial</router-link>
    .
  </p>

  <ContentHeader id="reference-sections">Sections</ContentHeader>

  <h3>
    <router-link to="/docs/reference/container">The Container</router-link>
  </h3>
  <p>
    The workshop utilises a container to build and publish services. Learn how to register new services and replace
    existing ones.
  </p>

  <h3>
    <router-link to="/docs/reference/available-services">Available Services</router-link>
  </h3>
  <p>
    Learn about the services exposed by the container, many of them utilities you can use in your exercises, checks and
    other services.
  </p>

  <h3>
    <router-link to="/docs/reference/exercise-types">Exercise Types</router-link>
  </h3>
  <p>
    Exercises can be of either
    <Code>CLI</Code>
    or
    <Code>CGI</Code>
    type. Learn about the differences here and which one is right for your exercise.
  </p>

  <h3>
    <router-link to="/docs/reference/exercise-solutions">Exercise Solutions</router-link>
  </h3>
  <p>
    Exercise solutions can consist of one or many files, which could consist of an entire application. They can also
    contain Composer dependencies in order to model real-world scenarios.
  </p>

  <h3>
    <router-link to="/docs/reference/results">Results & Renderers</router-link>
  </h3>
  <p>Learn about Results and Result Renderers.</p>

  <h3>
    <router-link to="/docs/reference/exercise-checks">Exercise Checks</router-link>
  </h3>
  <p>Learn what Exercise Checks are and how to use them in your exercise.</p>

  <h3>
    <router-link to="/docs/reference/bundled-checks">Bundled Checks</router-link>
  </h3>
  <p>Learn about the available Exercise Checks in the workshop framework and how to use them.</p>

  <h3>
    <router-link to="/docs/reference/creating-simple-checks">Creating Simple Checks</router-link>
  </h3>
  <p>Learn how to create your own Simple Check.</p>

  <h3>
    <router-link to="/docs/reference/creating-custom-results">Creating Custom Results</router-link>
  </h3>
  <p>Create a custom result to use in your check.</p>

  <h3>
    <router-link to="/docs/reference/creating-custom-result-renderer">Creating Custom Result Renderers</router-link>
  </h3>
  <p>Create a renderer for your result.</p>

  <h3><router-link to="/docs/reference/events">Events</router-link></h3>
  <p>Learn about all the events available for listening to in the workshop framework and how to listen to them.</p>

  <h3>
    <router-link to="/docs/reference/creating-listener-checks">Creating Listener Checks</router-link>
  </h3>
  <p>Learn how to utilise events to build a Listener Check by following a tutorial to build a Couch DB check.</p>

  <h3>
    <router-link to="/docs/reference/self-checking-exercises">Self Checking Exercise</router-link>
  </h3>
  <p>Add simple checking logic directly in your exercise.</p>

  <h3>
    <router-link to="/docs/reference/exercise-events">Exercise Events</router-link>
  </h3>
  <p>Listen to events directly in your exercise instead of building a custom check.</p>

  <h3>
    <router-link to="/docs/reference/patching-exercise-solutions">Patching Exercise Solutions</router-link>
  </h3>
  <p>
    Learn how to modify the student's solution on the fly, injecting variables, wrapping code in in
    <Code>try/catch</Code>
    blocks, registering error handlers and so on.
  </p>
</template>
