<script setup>
import Code from "../../DocCode.vue";
import ContentHeader from "../../ContentHeader.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
  <p>
    The main task of the workshop framework is to compare the output of the student's solution with the output of the reference solution, it is not however, limited to this. In this article we will
    introduce the workshop exercise check feature.
  </p>

  <ContentHeader id="what-are-checks">What are Checks?</ContentHeader>
  <p>Checks are extra verifications that can be performed during the process of verifying a students solution to an exercise. Each exercise can utilise any amount of additional checks.</p>

  <ContentHeader id="check-types">Check Types</ContentHeader>
  <p>
    There are two types of checks:
    <Code>Simple</Code>
    &
    <Code>Listener</Code>
    . What are the differences? Read on!
  </p>

  <ContentHeader level="h4" id="simple-checks">Simple Checks</ContentHeader>
  <p>
    Simple checks are basically a block of code that can run before or after verifying the output of the students solution. There can be many checks, which will be run sequentially - each check can
    return a success or failure and this will be injected into the result set.
  </p>

  <ContentHeader level="h4" id="listener-checks">Listener Checks</ContentHeader>
  <p>
    Listener checks are more advanced - when you add a listener type check, it will be passed an event dispatcher (
    <Code>PhpSchool\PhpWorkshop\Event\EventDispatcher</Code>
    ) which can be used to listen to various events throughout the life-cycle of verifying. Learn more about Listener Checks and the events which can be listened to in the
    <router-link to="/docs/reference/creating-listener-checks">Listener checks documentation</router-link>
    .
  </p>

  <ContentHeader id="how-to-use">How to use a check in your exercise?</ContentHeader>

  <ContentHeader level="h4" id="step-1">1. Tell the dispatcher about your check requirements</ContentHeader>
  <p>
    In order to specify that your exercise requires additional checks you should implement the method
    <Code>configure</Code>
    in your exercise. It will be passed an instance of
    <Code>ExerciseDispatcher</Code>
    which you can interact with and tell it which checks your exercise requires. The method
    <Code>configure</Code>
    is already implemented in
    <Code>AbstractExercise</Code>
    , but is empty, so you don't need to call
    <Code>parent::configure($dispatcher)</Code>
    inside your method.
  </p>

  <CodeBlock lang="php">
    <pre>
class MyExercise extends AbstractExercise implements ExerciseInterface
{
    ...snip

    /**
     * @param ExerciseDispatcher $dispatcher
     */
    public function configure(ExerciseDispatcher $dispatcher)
    {
        $dispatcher->requireCheck(ComposerCheck::class);
    }
}
</pre
    >
  </CodeBlock>

  <p>
    This basically informs the workshop framework that when verifying the student's solution to this exercise, we should also run the
    <Code>ComposerCheck</Code>
    check. To learn what the
    <Code>ComposerCheck</Code>
    check actually does go to the
    <router-link to="/docs/reference/bundled-checks">Bundled Checks</router-link>
    page.
  </p>

  <ContentHeader level="h4" id="step-2">2. Implement the required interface and methods</ContentHeader>
  <p>
    The second and final step is to implement the correct interface in your exercise. If you do not do this the workshop framework will throw an exception when it tries to run the check. Each check
    has an interface you need to implement when requiring it in your exercise. This interface can be found by visiting the
    <router-link to="/docs/reference/bundled-checks">Bundled Checks</router-link>
    page or by looking at the
    <Code>getExerciseInterface()</Code>
    method of the check. This method returns a string containing the FQCN (Fully Qualified Class Name) of the interface the check requires your exercise to implement.
  </p>

  <p>
    Some of the bundled checks only require you to implement
    <Code>PhpSchool\PhpWorkshop\Exercise\ExerciseInterface</Code>
    which you will have to do anyway as part of building your exercise. Some checks require you to implement additional interfaces which introduce new methods to your exercise. These methods provide
    the checks with the necessary information to execute. For example, the
    <Code>ComposerCheck</Code>
    requires you to implement the
    <Code>PhpSchool\PhpWorkshop\ExerciseCheck\ComposerExerciseCheck</Code>
    interface, which, in turn, requires your exercise to implement the method
    <Code>getRequiredPackages</Code>
    .
  </p>

  <p>
    So, your exercise, taking advantage of the
    <Code>ComposerExerciseCheck</Code>
    may end up looking something like the following:
  </p>

  <CodeBlock lang="php">
    <pre>
class MyExercise extends AbstractExercise implements
        ExerciseInterface,
        ComposerExerciseCheck
{
    ...snip

    /**
     * @param ExerciseDispatcher $dispatcher
     */
    public function configure(ExerciseDispatcher $dispatcher)
    {
        $dispatcher->requireCheck(ComposerCheck::class);
    }

    /**
     * @return array
     */
    public function getRequiredPackages()
    {
        return [
            'nikic/fast-route'
        ];
    }

}
</pre
    >
  </CodeBlock>
</template>
