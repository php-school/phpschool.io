<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

import DocList from './List.vue';
import DocListItem from './ListItem.vue';
import EventDescription from "./EventDescription.vue";

const tabs = [
    'CLI Verify',
    'CLI Run',
    'CGI Verify',
    'CGI Run',
]
</script>

<template>
    <TabGroup as="div" class="">
        <TabList class="flex justify-between">
            <Tab as="template" v-slot="{ selected }" v-for="tab in tabs">
                <button :class="[selected ? 'border-t border-pink-600 border-x border-x-gray-600 border-b-0 text-white' : ' hover:border-b hover:border-pink-600', 'px-3 py-2 text-lg font-medium w-full border-b border-b-gray-600 focus:outline-none']">
                    {{tab}}
                </button>
            </Tab>
        </TabList>
        <TabPanels>
            <TabPanel class="p-6 border-b border-l border-r border-gray-600 focus:outline-none">
                <DocList title="CLI Verify Events">
                    <DocListItem>
                        <EventDescription event="route.pre.resolve.args" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{name: 'command', type: 'callable'}]">
                            This event is triggered before the arguments to a command are resolved. Resolving arguments checks that all required arguments have been passed to the command.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="route.pre.invoke" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[]">
                            This event is triggered just before a command is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.start" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the start of the exercise dispatcher verification process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.pre.execute" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered after the <strong>before</strong> checks have successfully finished running, and before the exercise is passed to the specific exercise runner.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.verify.start" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the start of the CLI runner verification process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.verify.reference-execute.pre" event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered just before the reference solution is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.verify.reference.executing" event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered while the reference solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.verify.reference-execute.fail" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is only triggered if the reference solution failed to execute correctly, that is, it returned a non-zero exit code.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.verify.student-execute.pre" event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered just before the student's solution is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.verify.student.executing" event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered while the student's solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.verify.student-execute.fail" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is only triggered if the student's solution failed to execute correctly, that is, it returned a non-zero exit code.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.verify.finish" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the end of the CLI runner verification process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.post.execute" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered after the exercise runner has finished and right before the after checks are run.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.post.check" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered after the <strong>after</strong> checks have finished running.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.finish" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the end of the exercise dispatcher verification process.
                        </EventDescription>
                    </DocListItem>
                </DocList>
            </TabPanel>
            <TabPanel class="p-6 border-b border-l border-r border-gray-600 focus:outline-none">
                <DocList title="CLI Run Events">
                    <DocListItem>
                        <EventDescription event="route.pre.resolve.args" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{name: 'command', type: 'callable'}]">
                            This event is triggered before the arguments to a command are resolved. Resolving arguments checks that all required arguments have been passed to the command.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="route.pre.invoke" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[]">
                            This event is triggered just before a command is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="run.start" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the start of the exercise dispatcher run process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.run.start" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the start of the CLI runner run process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.run.student-execute.pre" event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered just before the student's solution is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.run.student.executing" event-class="PhpSchool\PhpWorkshop\Event\CliExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered while the student's solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cli.run.finish" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the end of the CLI runner run process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="run.finish" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the end of the exercise dispatcher run process.
                        </EventDescription>
                    </DocListItem>
                </DocList>
            </TabPanel>
            <TabPanel class="p-6 border-b border-l border-r border-gray-600 focus:outline-none">
                <DocList title="CGI Verify Events">
                    <DocListItem>
                        <EventDescription event="route.pre.resolve.args" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{name: 'command', type: 'callable'}]">
                            This event is triggered before the arguments to a command are resolved. Resolving arguments checks that all required arguments have been passed to the command.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="route.pre.invoke" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[]">
                            This event is triggered just before a command is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.start" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the start of the exercise dispatcher verification process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.pre.execute" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered after the <strong>before</strong> checks have successfully finished running, and before the exercise is passed to the specific exercise runner.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.verify.start" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the start of the CGI runner verification process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.verify.reference-execute.pre" event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered just before the reference solution is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.verify.reference.executing" event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered while the reference solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.verify.reference-execute.fail" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is only triggered if the reference solution failed to execute correctly, that is, it returned a non-zero exit code.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.verify.student-execute.pre" event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered just before the student's solution is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.verify.student.executing" event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered while the student's solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.verify.student-execute.fail" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is only triggered if the student's solution failed to execute correctly, that is, it returned a non-zero exit code.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.verify.finish" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the end of the CGI runner verification process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.post.execute" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered after the exercise runner has finished and right before the after checks are run.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.post.check" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered after the <strong>after</strong> checks have finished running.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="verify.finish" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the end of the exercise dispatcher verification process.
                        </EventDescription>
                    </DocListItem>
                </DocList>
            </TabPanel>
            <TabPanel class="p-6 border-b border-l border-r border-gray-600 focus:outline-none">
                <DocList title="CGI Run Events">
                    <DocListItem>
                        <EventDescription event="route.pre.resolve.args" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[{name: 'command', type: 'callable'}]">
                            This event is triggered before the arguments to a command are resolved. Resolving arguments checks that all required arguments have been passed to the command.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="route.pre.invoke" event-class="PhpSchool\PhpWorkshop\Event\Event" :args="[]">
                            This event is triggered just before a command is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="run.start" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the start of the exercise dispatcher run process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.run.start" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the start of the CGI runner run process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.run.student-execute.pre" event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered just before the student's solution is executed.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.run.student.executing" event-class="PhpSchool\PhpWorkshop\Event\CgiExecuteEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered while the student's solution is being executed. Here you can actually interact with the program, for example if it kicked of a TCP server.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="cgi.run.finish" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the end of the CGI runner run process.
                        </EventDescription>
                    </DocListItem>
                    <DocListItem>
                        <EventDescription event="run.finish" event-class="PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent" :args="[{name: 'exercise', type: 'PhpSchool\\PhpWorkshop\\Exercise\\ExerciseInterface'}, {name: 'fileName', type: 'string'}]">
                            This event is triggered right at the end of the exercise dispatcher run process.
                        </EventDescription>
                    </DocListItem>
                </DocList>
            </TabPanel>
        </TabPanels>
    </TabGroup>
</template>

