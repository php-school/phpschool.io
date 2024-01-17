<script setup>
import Code from '../../DocCode.vue'
import ContentHeader from '../../ContentHeader.vue'
import ListItem from '../../DocListItem.vue'
import List from '../../DocList.vue'
import BundledCheck from '../../BundledCheck.vue'
import CodeBlock from '../../CodeBlock.vue'
</script>
<template>
    <p>
        This article documents each of the checks bundled with the workshop framework and how to use
        them. Remember the exercise types from the
        <router-link to="/docs/reference/exercise-types">Exercise Types documentation</router-link>?
        <router-link to="/docs/reference/exercise-types#cli">CLI</router-link>,
        <router-link to="/docs/reference/exercise-types#cgi">CGI</router-link> &
        <router-link to="/docs/reference/exercise-types#custom">CUSTOM</router-link>.
    </p>

    <p>
        Well, checks can support one or more of these exercise types types. So inspect the table
        below to see if the check you want to use actually supports your exercise type.
    </p>

    <List title="Bundled Checks">
        <ListItem>
            <BundledCheck
                check="PhpSchool\PhpWorkshop\Check\FileExistsCheck"
                interface-to-implement="PhpSchool\PhpWorkshop\Exercise\ExerciseInterface"
            >
                This check verifies that the student's solution file actually exists. This check is
                always registered as the first check and verifying will abort if it fails.
            </BundledCheck>
        </ListItem>
        <ListItem>
            <BundledCheck
                check="PhpSchool\PhpWorkshop\Check\CodeParseCheck"
                interface-to-implement="PhpSchool\PhpWorkshop\Exercise\ExerciseInterface"
            >
                This check verifies that the student's solution file can actually be parsed. Parsing
                is done with
                <a href="https://github.com/nikic/PHP-Parser">nikic/php-parser</a>
            </BundledCheck>
        </ListItem>
        <ListItem>
            <BundledCheck
                check="PhpSchool\PhpWorkshop\Check\PhpLintCheck"
                interface-to-implement="PhpSchool\PhpWorkshop\Exercise\ExerciseInterface"
            >
                This check verifies that the student's solution file contains valid PHP syntax. This
                is as simple as <Code>php -l &lt;submission-file&gt;</Code>
            </BundledCheck>
        </ListItem>
        <ListItem>
            <BundledCheck
                check="PhpSchool\PhpWorkshop\Check\FunctionRequirementsCheck"
                interface-to-implement="PhpSchool\PhpWorkshop\Exercise\FunctionRequirementsExerciseCheck"
                :registered="false"
                link="#check-functional-requirements"
            >
                This check verifies that the students submission contains usages of some required
                functions and also does not use certain functions. This check is useful if you want
                to ban a certain way of achieving something, for example, teaching how to manually
                write a function that already existing in the standard library.
            </BundledCheck>
        </ListItem>
        <ListItem>
            <BundledCheck
                check="PhpSchool\PhpWorkshop\Check\ComposerCheck"
                interface-to-implement="PhpSchool\PhpWorkshop\Exercise\ComposerExerciseCheck"
                :registered="false"
                link="#check-composer"
            >
                This check verifies that the student used Composer to install the required
                dependencies of the exercise. It checks that a
                <Code>composer.lock</Code> files exists and contains entries for the required
                packages.
            </BundledCheck>
        </ListItem>
        <ListItem>
            <BundledCheck
                check="PhpSchool\PhpWorkshop\Check\DatabaseCheck"
                interface-to-implement="PhpSchool\PhpWorkshop\Exercise\DatabaseExerciseCheck"
                type="Listener"
                :registered="false"
                link="#check-database"
            >
                This check sets up a database and a <Code>PDO</Code> object. It prepends the
                database DSN as a CLI argument to the student's solution so they can connect to the
                database. The <Code>PDO</Code> object is passed to the exercise before and after the
                student's solution has been executed, allowing you to first seed the database and
                then verify the contents of the database.
            </BundledCheck>
        </ListItem>
    </List>

    <ContentHeader id="check-functional-requirements">Functional Requirements Check</ContentHeader>
    <p>
        Here is an example of how to force the student to use the function
        <Code>curl_exec</Code> and ban the usage of <Code>file_get_contents</Code>. This could be
        useful if you wanted to teach advanced configuration of data transfers.
    </p>

    <CodeBlock lang="php">
        <pre>
class MyExercise extends AbstractExercise
        implements ExerciseInterface, FunctionRequirementsExerciseCheck
{
    ...snip

    /**
     * @param ExerciseDispatcher $dispatcher
     */
    public function configure(ExerciseDispatcher $dispatcher)
    {
        $dispatcher->requireCheck(FunctionRequirementsCheck::class);
    }

    /**
     * @return string[]
     */
    public function getRequiredFunctions()
    {
        return ['curl_exec'];
    }

    /**
     * @return string[]
     */
    public function getBannedFunctions()
    {
        return ['file_get_contents'];
    }
}
</pre
        >
    </CodeBlock>

    <p>If a student submitted something like:</p>
    <CodeBlock lang="php">
        <pre>
&lt;?php

echo file_get_contents('http://example.com');
</pre
        >
    </CodeBlock>

    <p>The output would be something along the lines of:</p>
    <a href="/img/func-requirements.png"
        ><img src="../../../../../img/cloud/docs/func-requirements.png" class="doc-terminal-screen"
    /></a>

    <ContentHeader id="check-composer">Composer Check</ContentHeader>
    <p>
        Here is an example of how to force the student to require the
        <Code>nikic/fast-route</Code> package via Composer. This is useful if you want to focus on a
        specific problem or promote popular/battle tested packages.
    </p>

    <CodeBlock lang="php">
        <pre>
class MyExercise extends AbstractExercise
        implements ExerciseInterface, ComposerExerciseCheck
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

    <p>
        A number of failures can occur here, the student may not even have a
        <Code>composer.json</Code> or a <Code>composer.lock</Code> file. If this is the case, the
        check will fail and a message will be printed. If the required files are present, but the
        package has not been found, the output will look like the following:
    </p>

    <a href="/img/composer-check.png"
        ><img src="../../../../../img/cloud/docs/composer-check.png" class="doc-terminal-screen"
    /></a>

    <ContentHeader id="check-database">Database Check</ContentHeader>
    <p>
        Here is an example where we require that the student insert a record in to the
        <Code>users</Code> table which we will create in the <Code>seed</Code> method of our check.
        We will then verify that there are some records in the <Code>users</Code> table.
    </p>

    <CodeBlock lang="php">
        <pre>
class MyExercise extends AbstractExercise
        implements ExerciseInterface, DatabaseExerciseCheck
{
    ...snip

    /**
     * @param ExerciseDispatcher $dispatcher
     */
    public function configure(ExerciseDispatcher $dispatcher)
    {
        $dispatcher->requireCheck(DatabaseCheck::class);
    }

    /**
     * @param PDO $db
     * @return void
     */
    public function seed(PDO $db)
    {
        $db->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, age INTEGER, gender TEXT)');
    }

    /**
     * @param PDO $db
     * @return bool
     */
    public function verify(PDO $db)
    {
        return $db->query('select COUNT(*) from users')->fetchColumn() > 0;
    }
}
</pre
        >
    </CodeBlock>

    <p>
        If the student did not insert into the database, for example, if they submitted the
        following:
    </p>
    <CodeBlock lang="php">
        <pre>
&lt;?php

echo "Where is the database?";
</pre
        >
    </CodeBlock>
    <p>Then they would receive the following failure:</p>

    <a href="/img/database-check-failure.png"
        ><img
            src="../../../../../img/cloud/docs/database-check-failure.png"
            class="doc-terminal-screen"
    /></a>
    <p>If they submitted a proper solution, like the following:</p>
    <CodeBlock lang="php">
        <pre>
&lt;?php

$dsn = $argv[1];
$db = new PDO($dsn);
$stmt = $db->prepare('INSERT INTO users (name, age, gender) VALUES (:name, :age, :gender)');
$stmt->execute([':name' => 'Karl Renner', ':age' => 80, ':gender' => 'Male']);

</pre
        >
    </CodeBlock>
    <p>They would receive this success:</p>

    <a href="/img/database-check-success.png"
        ><img
            src="../../../../../img/cloud/docs/database-check-success.png"
            class="doc-terminal-screen"
    /></a>
</template>
