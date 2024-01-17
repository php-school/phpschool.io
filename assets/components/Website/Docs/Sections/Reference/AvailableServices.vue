<script setup>
import Note from '../../DocNote.vue'
import List from '../../DocList.vue'
import ListItem from '../../DocListItem.vue'
</script>
<template>
    <p>
        There are numerous services exposed via the DI container. The services exposed to user-land
        code are listed below. These can be used in your exercises, custom-checks and other services
        you may create as part of your workshop.
    </p>

    <Note type="warning"
        >There are actually many more services exposed but these are for internal use of the
        workshop framework only. These services should not be used. If you decide to ignore the
        advice given here, you should take caution as backwards compatibility breaks may be
        introduced in minor versions. If you feel a service should be public, contact us and we can
        discuss it!</Note
    >

    <List title="Public Services">
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">Colors\Color</h3>
            <p class="text-xs">
                An instance of
                <a href="https://github.com/kevinlebrun/colors.php">Colors\Color</a>. Use this to
                apply ANSI styles to strings. For example, to colour a string ready to be printed to
                the console.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">
                PhpSchool\PhpWorkshop\Output\OutputInterface
            </h3>
            <p class="text-xs">
                A utility class to print content to the standard output stream. Currently this is
                the standard out connected to the terminal.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">
                PhpSchool\PhpWorkshop\ExerciseRepository
            </h3>
            <p class="text-xs">
                This is a repository of exercises allowing to retrieve exercises and get information
                regarding them.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">
                PhpSchool\PhpWorkshop\ExerciseRepository
            </h3>
            <p class="text-xs">
                This is a repository of exercises allowing to retrieve exercises and get information
                regarding them.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">
                PhpSchool\PhpWorkshop\Event\EventDispatcher
            </h3>
            <p class="text-xs">
                EventDispatcher allows listening to events and inserting verifiers at certain points
                throughout the verifying
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">
                Symfony\Component\Filesystem\Filesystem
            </h3>
            <p class="text-xs">
                An instance of
                <a href="http://symfony.com/doc/current/components/filesystem/introduction.html"
                    >Symfony\Component\Filesystem\Filesystem</a
                >
                allowing to perform simple IO operations.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">PhpParser\Parser</h3>
            <p class="text-xs">
                An instance of
                <a href="https://github.com/nikic/PHP-Parser">PhpParser\Parser</a> which allows to
                parse PHP code in to an AST structure.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">PhpSchool\PhpWorkshop\CodePatcher</h3>
            <p class="text-xs">Utility to apply patches to PHP code from Exercises.</p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">
                PhpSchool\CliMenu\Terminal\TerminalInterface
            </h3>
            <p class="text-xs">
                An implementation of
                <a
                    href="https://github.com/php-school/cli-menu/blob/master/src/Terminal/TerminalInterface.php"
                    >PhpSchool\CliMenu\Terminal\TerminalInterface</a
                >
                which allows to retrieve and control the current terminal. For example getting the
                current terminal width.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">
                PhpSchool\PhpWorkshop\MarkdownRenderer
            </h3>
            <p class="text-xs">
                A utility to render Markdown to a string which should display nicely in a console
                terminal using ANSI escape sequences to format the output.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">PhpSchool\PhpWorkshop\UserState</h3>
            <p class="text-xs">
                This object provides access to the list of the exercises the student has completed
                and the exercise they are currently studying.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">PhpSchool\PSX\SyntaxHighlighter</h3>
            <p class="text-xs">
                An instance of
                <a href="https://github.com/php-school/psx#usage"
                    >PhpSchool\PSX\SyntaxHighlighter</a
                >
                which highlights PHP code using ANSI escape sequences ready to output on a console
                terminal.
            </p>
        </ListItem>
        <ListItem>
            <h3 class="font-mono font-bold text-base mb-2">
                PhpSchool\PhpWorkshop\Factory\ResultRendererFactory
            </h3>
            <p class="text-xs">
                Add your own renderer for your custom results or replace an existing renderer, with
                your own implementation.
            </p>
        </ListItem>
    </List>
</template>
