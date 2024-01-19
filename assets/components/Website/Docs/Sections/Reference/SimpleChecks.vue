<script setup>
import Code from "../../DocCode.vue";
import Note from "../../DocNote.vue";
import ContentHeader from "../../ContentHeader.vue";
import Terminal from "../../DocTerminal.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
  <p>
    In this article we will learn how to create a simple check. If you don't fully understand what checks are, head over to the
    <router-link to="/docs/reference/exercise-checks">Exercise Checks</router-link>
    article.
  </p>

  <p>We will build a fairly boring check which verifies that a student's solution passes the PSR2 coding standard. Lets get started!</p>

  <p>
    Creating a check begins with creating a file and a class for our check . We need to implement the interface
    <Code>PhpSchool\PhpWorkshop\Check\SimpleCheckInterface</Code>
    which extends from
    <Code>PhpSchool\PhpWorkshop\Check\CheckInterface</Code>
    . Let's breakdown these methods before we start coding:
  </p>

  <ContentHeader level="h4-code" id="get-name">getName()</ContentHeader>
  <p>
    This method should just return a
    <code>string</code>
    which represents the name of the check. This will be printed on the terminal during the verification process. This will be
    <code>PSR2 Code Check</code>
    for our check.
  </p>

  <ContentHeader level="h4-code" id="get-exercise-interface">getExerciseInterface()</ContentHeader>
  <p>
    This method should just return a
    <Code>string</Code>
    which is the FQCN (Fully Qualified Class Name) of the interface that the exercise needs to implement when requiring our check. Because we don't need any extra information for our check we can just
    use
    <Code>PhpSchool\PhpWorkshop\Exercise\ExerciseInterface</Code>
    .
  </p>

  <ContentHeader level="h4-code" id="can-run">canRun(ExerciseType $exerciseType)</ContentHeader>
  <p>
    This method receives an
    <Code>ExerciseType</Code>
    instance which represents the type of exercise, we use this to inform the workshop which exercise types our check works with:
    <Code>CLI</Code>
    ,
    <Code>CGI</Code>
    or
    <Code>CUSTOM</Code>
    . We will support
    <Code>CLI</Code>
    &
    <Code>CGI</Code>
    .
  </p>

  <ContentHeader level="h4-code" id="check">check(ExerciseInterface $exercise, Input $input)</ContentHeader>
  <p>
    This is the method where we actually perform our check logic, executing PHP_CodeSniffer. This method receives an instance of the current exercise and the input arguments passed to our workshop,
    which will contain the file name of the student's solution.
  </p>

  <p>
    This method needs to return an instance of
    <Code>PhpSchool\PhpWorkshop\Result\ResultInterface</Code>
    . Depending on whether you want a success or failure to be recorded it will be an instance of
    <Code>PhpSchool\PhpWorkshop\Result\SuccessInterface</Code>
    or
    <Code>PhpSchool\PhpWorkshop\Result\FailureInterface</Code>
    . Learn about results
    <router-link to="/docs/reference/results">here</router-link>
    .
  </p>

  <ContentHeader level="h4-code" id="get-position">getPosition()</ContentHeader>

  <p>
    This method should return one of two constants
    <Code>PhpSchool\PhpWorkshop\Check\SimpleCheckInterface::CHECK_BEFORE</Code>
    ,
    <Code>PhpSchool\PhpWorkshop\Check\SimpleCheckInterface::CHECK_AFTER</Code>
    . This value indicates when the check should be run.
  </p>

  <ContentHeader id="verification-process">The verification process</ContentHeader>

  <p>The process of verifying a student's solution looks something like the following (pseudo code):</p>

  <CodeBlock lang="php">
    <pre>
//run before checks
foreach ($beforeChecks as $check) {
    $result = $check->check($exercise, $submissionFilePath);

    if (!$result->isSuccessful()) {
        return;
    }
}

//compare output of student solution and reference solution
$this->verifier->compareOutput($exercise);

foreach ($afterChecks as $check) {
    $result = $check->check($exercise, $submissionFilePath);
    //store result
}
</pre
    >
  </CodeBlock>

  <ContentHeader level="h4" id="before-verifying">Before Verifying</ContentHeader>
  <p>
    When a check uses
    <Code>CHECK_BEFORE</Code>
    mode it is run before the output verification. The process is also short circuited if a check returns a failure. No more checks will be run and the output will not be compared.
  </p>

  <ContentHeader level="h4" id="after-verifying">After Verifying</ContentHeader>
  <p>
    When a check use
    <Code>CHECK_AFTER</Code>
    mode it is run after the output verification. This means that the check is run after the student's solution has been run. After checks are useful for verifying that something was actually
    performed in the students submission, for example, inserting a row into the database.
  </p>

  <ContentHeader id="build-check">Build the check</ContentHeader>

  <p>
    Now - let's build it! We will use the already built tutorial workshop as a base - the finished code is available on the
    <Code>custom-simple-check</Code>
    branch of the
    <a href="https://github.com/php-school/simple-math">tutorial repository</a>
    . We will start fresh from the
    <Code>master</Code>
    branch for this tutorial, so if you haven't already got it, git clone it and install the dependencies:
  </p>

  <Terminal :lines="['cd projects', 'git clone git@github.com:php-school/simple-math.git', 'cd simple-math', 'composer install']"></Terminal>

  <p>
    Our check will run the
    <a href="https://github.com/squizlabs/PHP_CodeSniffer">PHP_CodeSniffer</a>
    tool against the student's submission and report a success or failure based on the result.
  </p>

  <ContentHeader level="h4" id="check-step-1">1. Require the PHP_CodeSniffer tool as a dependency</ContentHeader>
  <Terminal :lines="['composer require squizlabs/php_codesniffer']"></Terminal>

  <ContentHeader level="h4" id="check-step-2">2. Create the folder and class</ContentHeader>
  <Terminal :lines="['mkdir src/Check', 'touch src/Check/Psr2Check.php']"></Terminal>

  <ContentHeader level="h4" id="check-step-3">3. Write the class</ContentHeader>
  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\SimpleMath\Check;

use PhpSchool\PhpWorkshop\Check\SimpleCheckInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;
use PhpSchool\PhpWorkshop\Result\Failure;
use PhpSchool\PhpWorkshop\Result\Success;
use PhpSchool\PhpWorkshop\Input\Input;

class Psr2Check implements SimpleCheckInterface
{

    public function getName()
    {
        return 'PSR2 Code Check';
    }

    public function getExerciseInterface()
    {
        return ExerciseInterface::class;
    }

    public function canRun(ExerciseType $exerciseType)
    {
        return in_array($exerciseType->getValue(), [ExerciseType::CGI, ExerciseType::CLI]);
    }

    public function check(ExerciseInterface $exercise, Input $input)
    {
        $phpCsBinary = __DIR__ . '/../../vendor/bin/phpcs';
        $cmd = sprintf('%s %s --standard=PSR2', $phpCsBinary, $input->getArgument('program'));
        exec($cmd, $output, $exitCode);

        if ($exitCode === 0) {
            return new Success($this->getName());
        }

        return new Failure($this->getName(), 'Coding style did not conform to PSR2!');
    }

    public function getPosition()
    {
        return static::CHECK_BEFORE;
    }
}
</pre
    >
  </CodeBlock>

  <p>
    If the
    <Code>phpcs</Code>
    binary returns a non-zero exit code - a failure occurred: probably the solution did not pass the coding standard check. So we return a failure with an error message. Otherwise a Success is
    returned.
  </p>

  <Note type="info">
    As we brought in the tool via Composer, we can rest assured that the binary
    <code>phpcs</code>
    is available in our projects
    <Code>vendor</Code>
    directory.
  </Note>

  <ContentHeader level="h4" id="check-step-4">4. Register the Check and add a factory</ContentHeader>
  <p>
    Now you need to tell the application about your new check. We need to register a factory.
    <router-link to="/docs/reference/container">What's a factory?</router-link>
    Open up
    <Code>app/config.php</Code>
    and add an entry for your check. The resulting file should look like:
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

use function DI\factory;
use function DI\object;
use Interop\Container\ContainerInterface;
use PhpSchool\SimpleMath\Check\Psr2Check;
use PhpSchool\SimpleMath\Exercise\Mean;
use Symfony\Component\Filesystem\Filesystem;

return [
    //Define your exercise factories here
    Mean::class => object(),

    //my checks
    Psr2Check::class => object(),
];
</pre
    >
  </CodeBlock>

  <p>
    Note the new entry for
    <Code>Psr2Check::class => object(),</Code>
    . Finally, we need to tell the application about our check in
    <Code>app/bootstrap.php</Code>
    . After the application object is created you just call
    <code>addCheck</code>
    with the name of check class. Your final
    <Code>app/bootstrap.php</Code>
    file should look something like:
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

ini_set('display_errors', 1);
date_default_timezone_set('Europe/London');
switch (true) {
    case (file_exists(__DIR__ . '/../vendor/autoload.php')):
        // Installed standalone
        require __DIR__ . '/../vendor/autoload.php';
        break;
    case (file_exists(__DIR__ . '/../../../autoload.php')):
        // Installed as a Composer dependency
        require __DIR__ . '/../../../autoload.php';
        break;
    case (file_exists('vendor/autoload.php')):
        // As a Composer dependency, relative to CWD
        require 'vendor/autoload.php';
        break;
    default:
        throw new RuntimeException('Unable to locate Composer autoloader; please run "composer install".');
}

use PhpSchool\PhpWorkshop\Application;
use PhpSchool\SimpleMath\Check\Psr2Check;
use PhpSchool\SimpleMath\Exercise\Mean;

$app = new Application('Simple Math', __DIR__ . '/config.php');

$app->addExercise(Mean::class);
$app->addCheck(Psr2Check::class);

$art =&lt;&lt;&lt;ART
  ∞ ÷ ∑ ×

 PHP SCHOOL
SIMPLE MATH
ART;

$app->setLogo($art);
$app->setFgColour('red');
$app->setBgColour('black');

return $app;
</pre
    >
  </CodeBlock>

  <ContentHeader level="h4" id="check-step-5">5. Require the check in an exercise</ContentHeader>
  <p>
    Open up the Mean Average exercise file:
    <Code>src/Exercise/Mean.php</Code>
    and add in the following method, take care to import the necessary classes (
    <Code>PhpSchool\PhpWorkshop\ExerciseDispatcher</Code>
    &
    <Code>PhpSchool\SimpleMath\Check\Psr2Check</Code>
    ):
  </p>

  <CodeBlock lang="php">
    <pre>
public function configure(ExerciseDispatcher $dispatcher)
{
    $dispatcher->requireCheck(Psr2Check::class);
}
</pre
    >
  </CodeBlock>
  <p>Hopefully you will remember this from the previous section - we are just telling the exercise to use our custom check!</p>

  <ContentHeader id="try-it-out">Try it out!</ContentHeader>

  <p>
    Run the workshop and select the Mean Average exercise. Verifying a solution which does not pass the
    <Code>PSR2</Code>
    coding standard will yield the output:
  </p>
  <a href="/img/custom-simple-check-psr2-fail.png">
    <img src="../../../../../img/cloud/docs/custom-simple-check-psr2-fail.png" class="doc-terminal-screen" />
  </a>
  <p>
    And a solution which
    <strong>does</strong>
    pass will yield the output:
  </p>
  <a href="/img/custom-simple-check-psr2-success.png">
    <img src="../../../../../img/cloud/docs/custom-simple-check-psr2-success.png" class="doc-terminal-screen" />
  </a>

  <ContentHeader id="custom-interface">Custom Interface</ContentHeader>
  <p>
    When you build checks, sometimes you need extra information from the exercise to configure the check. For example, the
    <a href="https://github.com/php-school/php-workshop/blob/master/src/Check/FunctionRequirementsCheck.php">FunctionRequirementsCheck</a>
    check calls
    <Code>getRequiredFunctions()</Code>
    &
    <Code>getBannedFunctions()</Code>
    on the exercise, these methods are defined on the extra interface
    <Code>FunctionRequirementsExerciseCheck</Code>
    which the exercise must implement if it requires the
    <Code>FunctionRequirementsCheck</Code>
    check.
  </p>

  <p>
    Maybe we want to make the standard for our check configurable - it could be
    <Code>PSR1</Code>
    ,
    <code>PSR2</code>
    ,
    <Code>PEAR</Code>
    or any of the other standards PHP_CodeSniffer supports. We will make this configuration available through the method
    <Code>getStandard()</Code>
    .
  </p>

  <ContentHeader level="h4" id="exercise-step-1">1. Define our interface</ContentHeader>
  <p>We need an interface to define our required method. Let's do that first:</p>

  <Terminal :lines="['mkdir src/ExerciseCheck', 'touch src/ExerciseCheck/Psr2ExerciseCheck.php']"></Terminal>

  <p>
    Now would probably be a good idea to change our check name to something a little less specific, but we'll leave that up to you, probably
    <Code>PhpCsCheck</Code>
    might be a little better. Okay, lets define our interface. We want the one method
    <Code>getStandard</Code>
    to return a string representing one of the
    <a href="https://github.com/squizlabs/PHP_CodeSniffer/tree/master/CodeSniffer/Standards">available standards</a>
    :
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\SimpleMath\ExerciseCheck;

interface Psr2ExerciseCheck
{
    /**
     * @return string
     */
    public function getStandard();
}
</pre
    >
  </CodeBlock>

  <ContentHeader level="h4" id="exercise-step-2">2. Update our check</ContentHeader>
  <p>
    We need to update the
    <Code>getExerciseInterface()</Code>
    method in our check to return the name of our new interface. Open up
    <Code>src/Check/Psr2Check.php</Code>
    and change the
    <Code>getExerciseInterface()</Code>
    method to match below:
  </p>

  <CodeBlock lang="php">
    <pre>
public function getExerciseInterface()
{
    return Psr2ExerciseCheck::class;
}
</pre
    >
  </CodeBlock>

  <p>
    We also need to modify our
    <Code>check()</Code>
    method to actually use this data:
  </p>

  <CodeBlock lang="php">
    <pre>
public function check(ExerciseInterface $exercise, $fileName)
{
    if (!$exercise instanceof Psr2ExerciseCheck) {
        throw new \InvalidArgumentException;
    }

    $standard = $exercise->getStandard();

    if (!in_array($standard, ['PSR1', 'PSR2', 'PEAR'])) {
        throw new \InvalidArgumentException('Standard is not supported');
    }

    $phpCsBinary = __DIR__ . '/../../vendor/bin/phpcs';
    $cmd = sprintf('%s %s --standard=%s', $phpCsBinary, $input->getArgument('program'), $standard);
    exec($cmd, $output, $exitCode);

    if ($exitCode === 0) {
        return new Success($this->getName());
    }

    return new Failure($this->getName(), 'Coding style did not conform to PSR2!');
}
</pre
    >
  </CodeBlock>
  <p>
    We've added a couple of things here - we make sure the exercise actually implements our required interface, if not we throw an exception. We check if the standard provided is in a small subset of
    supported standards, and finally, we pass the standard along to the
    <Code>phpcs</Code>
    command.
  </p>

  <ContentHeader level="h4" id="exercise-step-3">3. Update our exercise</ContentHeader>
  <p>
    Now we have to implement the new interface and methods in our exercise, for our Mean Average exercise, we will still require
    <Code>PSR2</Code>
    as the coding standard. The final exercise should look similar to below:
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\SimpleMath\Exercise;

use PhpSchool\PhpWorkshop\Exercise\AbstractExercise;
use PhpSchool\PhpWorkshop\Exercise\CliExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;
use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\SimpleMath\Check\Psr2Check;
use PhpSchool\SimpleMath\ExerciseCheck\Psr2ExerciseCheck;

class Mean extends AbstractExercise implements
    ExerciseInterface,
    CliExercise,
    Psr2ExerciseCheck
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Mean Average';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Simple Math';
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        $numArgs = rand(0, 10);

        $args = [];
        for ($i = 0; $i &lt; $numArgs; $i ++) {
            $args[] = rand(0, 100);
        }

        return $args;
    }

    /**
     * @return ExerciseType
     */
    public function getType()
    {
        return ExerciseType::CLI();
    }

    public function configure(ExerciseDispatcher $dispatcher)
    {
        $dispatcher->requireCheck(Psr2Check::class);
    }

    /**
     * @return string
     */
    public function getStandard()
    {
        return 'PSR2';
    }
}</pre
    >
  </CodeBlock>

  <p>You should be able to run it just the same as before we added the extra interface. You can now easily update your exercise to use a different coding standard without modifying the check.</p>

  <Note type="success">
    Maybe you could try updating the check to take into account the standard when returning the result? It currently has
    <Code>PSR2</Code>
    hardcoded in the message!
  </Note>

  <p>
    You can see the finished, working code on the
    <Code>custom-interface-check</Code>
    branch of the
    <a href="https://github.com/php-school/simple-math">tutorial repository</a>
    .
  </p>
</template>
