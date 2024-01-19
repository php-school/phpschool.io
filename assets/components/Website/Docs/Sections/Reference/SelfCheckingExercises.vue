<script setup>
import Code from "../../DocCode.vue";
import Note from "../../DocNote.vue";
import ContentHeader from "../../ContentHeader.vue";
import DocTerminal from "../../DocTerminal.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
  <p>
    As we have seen in the previous articles, you can build your own custom checks. Checks can be used in as many
    exercises as you want - you could even create a package which consists of common checks you might want to use in
    your workshops. The check we built in the
    <router-link to="/docs/reference/creating-simple-checks">Creating Simple Checks</router-link>
    article is a good example of a reusable check; you might want to include this in all your exercises.
  </p>
  <p>
    But what if you want to perform a check that you don't think you will use again? You don't really want to create a
    class to encompass this logic when it is only to be used in one exercise.
  </p>
  <p><em>Enter the Self Checking feature!</em></p>

  <p>
    The Self Checking feature allows your exercise to implement an interface which contains one method -
    <Code>check()</Code>
    during the verification process of the student's solution, your method will be called and passed the input arguments
    passed to our workshop, which will contain the file name of the student's solution. In this method you can do
    whatever you want: parse the code into an AST using the
    <Code>PhpParser\Parser</Code>
    service, lint it using a third party tool or whatever else you can think of.
  </p>

  <p>
    To give you an example of how you might use it - we use it
    <a href="https://github.com/php-school/learn-you-php/blob/master/src/Exercise/ConcernedAboutSeparation.php">
      here in Learn You PHP!
    </a>
    to check that a submission contains an
    <Code>include</Code>
    /
    <Code>require</Code>
    statement as the exercise is teaching how to separate code over multiple files. We want to enforce the student to
    include a separate file.
  </p>

  <ContentHeader id="creating-self-check-exercise">Creating a self checking exercise</ContentHeader>

  <p>
    Creating a self checking exercise requires implementing the interface
    <Code>PhpSchool\PhpWorkshop\ExerciseCheck\SelfCheck</Code>
    , adding your check logic and returning a
    <router-link to="/docs/reference/results">result</router-link>
    . Depending on whether you want a success or a failure to be recorded it will be an instance of
    <Code>PhpSchool\PhpWorkshop\Result\SuccessInterface</Code>
    or
    <Code>\PhpSchool\PhpWorkshop\Result\FailureInterface</Code>
    .
  </p>

  <p>The interface looks like this:</p>
  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\PhpWorkshop\ExerciseCheck;

use PhpSchool\PhpWorkshop\Result\ResultInterface;
use PhpSchool\PhpWorkshop\Input\Input;

interface SelfCheck
{
    /**
     * The method is passed the absolute file path to the student's solution and should return a result
     * object which indicates the success or not of the check.
     *
     * @param Input $input The command line arguments passed to the command.
     * @return ResultInterface The result of the check.
     */
    public function check(Input $input);
}
</pre
    >
  </CodeBlock>

  <p>You can implement like so:</p>
  <CodeBlock lang="php">
    <pre>
&lt;?php

class Mean extends AbstractExercise implements ExerciseInterface, CliExercise, SelfCheck
{

    ...omitting methods described in ExerciseInterface

    /**
     * @param Input $input
     * @return ResultInterface
     */
    public function check(Input $input)
    {
        //do some checking with $input

        if ($someResult) {
            return new Success('My Check');
        }

        return new Failure('My Check', "Something didn't go well!");
    }
}
</pre
    >
  </CodeBlock>

  <p>
    As you can see, you do the checking logic and then return a result object. The result object is used to render the
    results to the student. In this case the first argument to
    <Code>PhpSchool\PhpWorkshop\Result\Success</Code>
    is the name of the check being performed. The same is true for the failure
    <Code>PhpSchool\PhpWorkshop\Result\Failure</Code>
    , however, it takes an optional second argument which should describe what went wrong.
  </p>

  <Note type="success">
    Learn more about results
    <router-link to="/docs/reference/results">here</router-link>
    .
  </Note>

  <ContentHeader id="example-self-check">Example PSR2 self checking exercise</ContentHeader>
  <p>
    Contrary to what we said earlier (a PSR2 check would be a good candidate for a re-usable check), let's build that as
    a self check. We will use the already built example workshop as a base - the finished code is available on the
    <Code>self-checking-exercise</Code>
    branch of the
    <a href="https://github.com/php-school/simple-math">tutorial repository</a>
    .
  </p>
  <p>
    We will start fresh from the
    <Code>master</Code>
    branch for this tutorial, so if you haven't already got it, git clone it and install the dependencies:
  </p>

  <DocTerminal
    :lines="[
      'cd projects',
      'git clone git@github.com:php-school/simple-math.git',
      'cd simple-math',
      'composer install',
    ]"
  ></DocTerminal>

  <p>
    Our check will run the
    <a href="https://github.com/squizlabs/PHP_CodeSniffer">PHP_CodeSniffer</a>
    tool against the student's solution and report a success or failure based on the result.
  </p>

  <ContentHeader level="h4" id="check-step-1">1. Require the PHP_CodeSniffer tool as a dependency</ContentHeader>

  <DocTerminal :lines="['composer require squizlabs/php_codesniffer']"></DocTerminal>

  <ContentHeader level="h4" id="check-step-2">
    2. Modify the exercise to implement the SelfCheck interface
  </ContentHeader>
  <p>Our exercise should look like the following:</p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\SimpleMath\Exercise;

use PhpSchool\PhpWorkshop\Exercise\AbstractExercise;
use PhpSchool\PhpWorkshop\Exercise\CliExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;
use PhpSchool\PhpWorkshop\ExerciseCheck\SelfCheck;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\Result\Failure;
use PhpSchool\PhpWorkshop\Result\ResultInterface;
use PhpSchool\PhpWorkshop\Result\Success;

class Mean extends AbstractExercise implements
    ExerciseInterface,
    CliExercise,
    SelfCheck
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

    /**
     * @param Input $input
     * @return ResultInterface
     */
    public function check(Input $input)
    {

    }
}
</pre
    >
  </CodeBlock>

  <ContentHeader level="h4" id="check-step-3">3. Implement the check logic</ContentHeader>

  <p>
    As you can see, our check does nothing at the minute. Let's add the logic to execute
    <Code>phpcs</Code>
    on the student's solution using the
    <Code>PSR2</Code>
    standard. As we brought in the tool via Composer, we can rest assured that the binary
    <Code>phpcs</Code>
    is available in our projects
    <Code>vendor</Code>
    directory.
  </p>
  <p>Our method might look something like this - nothing new going on:</p>

  <CodeBlock lang="php">
    <pre>
/**
 * @param Input $input
 * @return ResultInterface
 */
public function check(Input $input)
{
    $phpCsBinary = __DIR__ . '/../../vendor/bin/phpcs';
    $cmd = sprintf('%s %s --standard=PSR2', $phpCsBinary, $input->getArgument('program'));
    exec($cmd, $output, $exitCode);

    if ($exitCode === 0) {
        return new Success('PSR2 Code Check');
    }

    return new Failure('PSR2 Code Check', 'Coding style did not conform to PSR2!');
}
</pre
    >
  </CodeBlock>

  <p>
    If the
    <Code>phpcs</Code>
    binary returns a non-zero exit code - a failure occurred: probably the solution did not pass the coding standard
    check. So we return a failure with an error message. Otherwise a Success is returned.
  </p>

  <p>
    Verifying a solution which does not pass the
    <Code>PSR2</Code>
    coding standard will yield the output:
  </p>
  <a href="/img/psr2-fail.png"><img src="../../../../../img/cloud/docs/psr2-fail.png" class="doc-terminal-screen" /></a>
  <p>
    And a solution which
    <strong>does</strong>
    pass would yield the output:
  </p>
  <a href="/img/psr2-success.png">
    <img src="../../../../../img/cloud/docs/psr2-success.png" class="doc-terminal-screen" />
  </a>
  <p>Hopefully this feature will help you build your workshops that bit faster!</p>
</template>
