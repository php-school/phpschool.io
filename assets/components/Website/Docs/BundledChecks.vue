<script setup>
import DocList from './List.vue';
import DocListItem from './ListItem.vue';
import Code from "./Code.vue";
import BundledCheck from "./BundledCheck.vue";
</script>

<template>
    <DocList title="Bundled Checks">
        <DocListItem>
            <BundledCheck check="PhpSchool\PhpWorkshop\Check\FileExistsCheck" interface="PhpSchool\PhpWorkshop\Exercise\ExerciseInterface">
                This check verifies that the student's solution file actually exists. This check is always registered as the first check and verifying will abort if it fails.
            </BundledCheck>
        </DocListItem>
        <DocListItem>
            <BundledCheck check="PhpSchool\PhpWorkshop\Check\CodeParseCheck" interface="PhpSchool\PhpWorkshop\Exercise\ExerciseInterface">
                This check verifies that the student's solution file can actually be parsed. Parsing is done with <a href="https://github.com/nikic/PHP-Parser">nikic/php-parser</a>
            </BundledCheck>
        </DocListItem>
        <DocListItem>
            <BundledCheck check="PhpSchool\PhpWorkshop\Check\PhpLintCheck" interface="PhpSchool\PhpWorkshop\Exercise\ExerciseInterface">
                This check verifies that the student's solution file contains valid PHP syntax. This is as simple as <Code>php -l &lt;submission-file&gt;</Code>
            </BundledCheck>
        </DocListItem>
        <DocListItem>
            <BundledCheck check="PhpSchool\PhpWorkshop\Check\FunctionRequirementsCheck" interface="PhpSchool\PhpWorkshop\Exercise\FunctionRequirementsExerciseCheck" :registered="false" link="#check-functional-requirements">
                This check verifies that the students submission contains usages of some required functions and also does not use certain functions. This check is useful if you want to ban a certain way of achieving something, for example, teaching how to manually write a function that already existing in the standard library.
            </BundledCheck>
        </DocListItem>
        <DocListItem>
            <BundledCheck check="PhpSchool\PhpWorkshop\Check\ComposerCheck" interface="PhpSchool\PhpWorkshop\Exercise\ComposerExerciseCheck" :registered="false" link="#check-composer">
                This check verifies that the student used Composer to install the required dependencies of the exercise. It checks that a <Code>composer.lock</Code> files exists and contains entries for the required packages.
            </BundledCheck>
        </DocListItem>
        <DocListItem>
            <BundledCheck check="PhpSchool\PhpWorkshop\Check\DatabaseCheck" interface="PhpSchool\PhpWorkshop\Exercise\DatabaseExerciseCheck" type="Listener" :registered="false" link="#check-database">
                This check sets up a database and a <Code>PDO</Code> object. It prepends the database DSN as a CLI argument to the student's solution so they can connect to the database. The <Code>PDO</Code> object is passed to the exercise before and after the student's solution has been executed, allowing you to first seed the database and then verify the contents of the database.
            </BundledCheck>
        </DocListItem>
    </DocList>
</template>