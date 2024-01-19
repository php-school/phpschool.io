<script setup>
import Code from "../../DocCode.vue";
import Note from "../../DocNote.vue";
import ContentHeader from "../../ContentHeader.vue";
import EventDescription from "../../EventDescription.vue";
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import DocListItem from "../../DocListItem.vue";
import DocList from "../../DocList.vue";
import CodeBlock from "../../CodeBlock.vue";

const tabs = ["CLI Verify", "CLI Run", "CGI Verify", "CGI Run"];
</script>
<template>
  <p>
    There are various events triggered throughout the verifying and running of exercises. These events can be used by
    <router-link to="/docs/reference/creating-listener-checks">Listener Checks</router-link>
    to hook in to the process at various stages. This article details the events and the arguments available.
  </p>

  <p>
    Each event implements
    <Code>PhpSchool\PhpWorkshop\Event\EventInterface</Code>
    where you can grab the parameters like
    <Code>$event->getParameter('myParam');</Code>
    some events may have convenience methods for accessing certain parameters, please refer to the particular event class for more info.
  </p>

  <p>There are 4 routes through the application, and the lists of events below each represent a timeline of one of those routes.</p>

  <h3 id="cli-verify">CLI Verify</h3>
  <p>
    This is the route taken when using the
    <Code>verify</Code>
    command on a
    <Code>CLI</Code>
    type exercise.
  </p>
  <h3 id="cli-run">CLI Run</h3>
  <p>
    This is the route taken when using the
    <Code>run</Code>
    command on a
    <Code>CLI</Code>
    type exercise.
  </p>
  <h3 id="cgi-verify">CGI Verify</h3>
  <p>
    This is the route taken when using the
    <Code>verify</Code>
    command on a
    <Code>CGI</Code>
    type exercise.
  </p>
  <h3 id="cgi-run">CGI Run</h3>
  <p>
    This is the route taken when using the
    <Code>run</Code>
    command on a
    <Code>CGI</Code>
    type exercise.
  </p>

  <ContentHeader id="listening">Listening to Events</ContentHeader>

  <p>Events can be listened to by attaching to the event dispatcher with any valid PHP callable:</p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

$eventDispatcher = $container->get(PhpSchool\PhpWorkshop\Event\EventDispatcher:class);
$eventDispatcher->listen('verify.start', function (Event $event) {
    //do something
});

// you can also listen to multiple events in one call

$eventDispatcher->listen(['verify.start', 'run.start'], function (Event $event) {
    //do something
});
</pre
    >
  </CodeBlock>

  <p>
    With the event dispatcher you can even do more interesting things, such as, at any event, you can insert a verifier (any valid PHP callable) - it will be passed the event, the same as a normal
    listener, but it must return an implementation of
    <Code>PhpSchool\PhpWorkshop\Result\ResultInterface</Code>
    . This will be evaluated and injected in to the results for reporting on the CLI.
    <Code>PhpSchool\PhpWorkshop\Result\SuccessInterface</Code>
    instances will be treated as successes and
    <Code>PhpSchool\PhpWorkshop\Result\FailureInterface</Code>
    instances will be treated as failures. So you can actually fail a verification attempt via an event.
  </p>

  <Note type="info">
    Learn more about results
    <router-link to="/docs/reference/results">here</router-link>
    .
  </Note>

  <p>
    This is useful for
    <em>Listener Checks</em>
    , for example, towards the end of the verifying process, you may want to verify that some data was inserted to a database. If it was not you will return a failure, which will be displayed on the
    CLI and will cause the verification attempt to fail.
  </p>

  <p>How to insert verifiers:</p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

$eventDispatcher = $container->get(PhpSchool\PhpWorkshop\Event\EventDispatcher:class);
$eventDispatcher->insertVerifier('verify.finish', function (Event $event) {
    if (!$this->checkDb()) {
        return Failure::fromNameAndReason('DB Check', 'DB Verification failed!');
    }
    return new Success('DB Check');
});
</pre
    >
  </CodeBlock>

  <ContentHeader id="event-list">Events</ContentHeader>

  <TabGroup as="div" class="mb-8">
    <TabList class="flex justify-between">
      <Tab as="template" v-slot="{ selected }" v-for="tab in tabs" :key="tab">
        <button
          :class="[
            selected ? 'border-x border-b-0 border-t border-pink-600 border-x-gray-600 text-white' : ' hover:border-b hover:border-pink-600',
            'w-full border-b border-b-gray-600 px-3 py-2 text-lg font-medium focus:outline-none',
          ]"
        >
          {{ tab }}
        </button>
      </Tab>
    </TabList>
    <TabPanels>
      <TabPanel class="border-b border-l border-r border-gray-600 p-6 pb-0 focus:outline-none">
        <DocList title="CLI Verify Events">
          <DocListItem>
            <EventDescription event="route.pre.resolve.args" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{ name: 'command', type: 'callable' }]">
              This event is triggered before the arguments to a command are resolved. Resolving arguments checks that all required arguments have been passed to the command.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription event="route.pre.invoke" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[]">This event is triggered just before a command is executed.</EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.start"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the start of the exercise dispatcher verification process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.pre.execute"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered after the
              <strong>before</strong>
              checks have successfully finished running, and before the exercise is passed to the specific exercise runner.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.verify.start"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the start of the CLI runner verification process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.verify.reference-execute.pre"
              event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered just before the reference solution is executed.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.verify.reference.executing"
              event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered while the reference solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.verify.reference-execute.fail"
              event-class="PhpSchool\PhpWorkshop\Event\Event"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is only triggered if the reference solution failed to execute correctly, that is, it returned a non-zero exit code.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.verify.student-execute.pre"
              event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered just before the student's solution is executed.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.verify.student.executing"
              event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered while the student's solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.verify.student-execute.fail"
              event-class="PhpSchool\PhpWorkshop\Event\Event"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is only triggered if the student's solution failed to execute correctly, that is, it returned a non-zero exit code.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.verify.finish"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the end of the CLI runner verification process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.post.execute"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered after the exercise runner has finished and right before the after checks are run.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.post.check"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered after the
              <strong>after</strong>
              checks have finished running.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.finish"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the end of the exercise dispatcher verification process.
            </EventDescription>
          </DocListItem>
        </DocList>
      </TabPanel>
      <TabPanel class="border-b border-l border-r border-gray-600 p-6 focus:outline-none">
        <DocList title="CLI Run Events">
          <DocListItem>
            <EventDescription event="route.pre.resolve.args" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{ name: 'command', type: 'callable' }]">
              This event is triggered before the arguments to a command are resolved. Resolving arguments checks that all required arguments have been passed to the command.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription event="route.pre.invoke" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[]">This event is triggered just before a command is executed.</EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="run.start"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the start of the exercise dispatcher run process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.run.start"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the start of the CLI runner run process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.run.student-execute.pre"
              event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered just before the student's solution is executed.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.run.student.executing"
              event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered while the student's solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cli.run.finish"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the end of the CLI runner run process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="run.finish"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the end of the exercise dispatcher run process.
            </EventDescription>
          </DocListItem>
        </DocList>
      </TabPanel>
      <TabPanel class="border-b border-l border-r border-gray-600 p-6 focus:outline-none">
        <DocList title="CGI Verify Events">
          <DocListItem>
            <EventDescription event="route.pre.resolve.args" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{ name: 'command', type: 'callable' }]">
              This event is triggered before the arguments to a command are resolved. Resolving arguments checks that all required arguments have been passed to the command.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription event="route.pre.invoke" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[]">This event is triggered just before a command is executed.</EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.start"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the start of the exercise dispatcher verification process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.pre.execute"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered after the
              <strong>before</strong>
              checks have successfully finished running, and before the exercise is passed to the specific exercise runner.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.verify.start"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the start of the CGI runner verification process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.verify.reference-execute.pre"
              event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered just before the reference solution is executed.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.verify.reference.executing"
              event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered while the reference solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.verify.reference-execute.fail"
              event-class="PhpSchool\PhpWorkshop\Event\Event"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is only triggered if the reference solution failed to execute correctly, that is, it returned a non-zero exit code.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.verify.student-execute.pre"
              event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered just before the student's solution is executed.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.verify.student.executing"
              event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered while the student's solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.verify.student-execute.fail"
              event-class="PhpSchool\PhpWorkshop\Event\Event"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is only triggered if the student's solution failed to execute correctly, that is, it returned a non-zero exit code.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.verify.finish"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the end of the CGI runner verification process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.post.execute"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered after the exercise runner has finished and right before the after checks are run.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.post.check"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered after the
              <strong>after</strong>
              checks have finished running.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="verify.finish"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the end of the exercise dispatcher verification process.
            </EventDescription>
          </DocListItem>
        </DocList>
      </TabPanel>
      <TabPanel class="border-b border-l border-r border-gray-600 p-6 focus:outline-none">
        <DocList title="CGI Run Events">
          <DocListItem>
            <EventDescription event="route.pre.resolve.args" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{ name: 'command', type: 'callable' }]">
              This event is triggered before the arguments to a command are resolved. Resolving arguments checks that all required arguments have been passed to the command.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription event="route.pre.invoke" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[]">This event is triggered just before a command is executed.</EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="run.start"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the start of the exercise dispatcher run process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.run.start"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the start of the CGI runner run process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.run.student-execute.pre"
              event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered just before the student's solution is executed.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.run.student.executing"
              event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered while the student's solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="cgi.run.finish"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the end of the CGI runner run process.
            </EventDescription>
          </DocListItem>
          <DocListItem>
            <EventDescription
              event="run.finish"
              event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent"
              :args="[
                {
                  name: 'exercise',
                  type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface',
                },
                { name: 'fileName', type: 'string' },
              ]"
            >
              This event is triggered right at the end of the exercise dispatcher run process.
            </EventDescription>
          </DocListItem>
        </DocList>
      </TabPanel>
    </TabPanels>
  </TabGroup>
</template>
