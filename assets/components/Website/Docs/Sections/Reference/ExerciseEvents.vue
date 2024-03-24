<script setup>
import Code from "../../DocCode.vue";
import Note from "../../DocNote.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
  <p>As we learned in the previous article, we can implement simple checking directly in our exercise if we don't want to build a check.</p>

  <p>However, Self Checking is rather un-flexible in that you can only hook in to the verify/run process at one particular point, immediately after all other checks have run.</p>

  <p>
    We also described the events features in previous chapters
    <router-link to="/docs/reference/events">Events</router-link>
    &
    <router-link to="/docs/reference/creating-listener-checks">Creating Listener Checks</router-link>
    . Well, as it goes, any events you can listen to in
    <em>Listener Checks</em>
    , you can listen to directly in your exercises!
  </p>

  <p>Check it out:</p>

  <CodeBlock lang="php">
    <pre>
&lt;?php
class MyExercise extends AbstractExercise implements ExerciseInterface
{
    ...snip

    /**
     * @param ExerciseDispatcher $dispatcher
     */
    public function configure(ExerciseDispatcher $dispatcher)
    {
        $dispatcher->requireCheck(ComposerCheck::class);

        $dispatcher->getEventDispatcher()->listen('verify.start', function (Event $e) {
            //do something
        });
    }
}
</pre
    >
  </CodeBlock>

  <p>
    You can grab the
    <Code>EventDispatcher</Code>
    from
    <Code>ExerciseDispatcher</Code>
    and listen to any events and insert verifiers just like we did in
    <router-link to="/docs/reference/creating-listener-checks#build-it">Creating Listener Checks</router-link>
    when we inserted a verifier function which verifies the state of a Couch DB database.
  </p>

  <Note type="success">
    You can listen to any of the events listed on
    <router-link to="/docs/reference/events">Events</router-link>
    .
  </Note>
</template>
