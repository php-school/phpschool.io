<script setup>
import Code from "../../DocCode.vue";
import Note from "../../DocNote.vue";
import ContentHeader from "../../ContentHeader.vue";
import DocTerminal from "../../DocTerminal.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
  <p>In this article we will build a custom result for our PSR2 check. In the next article, we will build the renderer for it, as the result is fairly useless without it.</p>

  <p>
    We want to be able to list out each of the coding standard violations as part of the feedback to the student. To do this, we will write our result (which is mostly a simple object containing the
    data) and then we will update our check to parse the violations and use the new result class.
  </p>

  <ContentHeader id="getting-started">Getting Started</ContentHeader>
  <p>If you are carrying on from the previous article then you can skip this first step of grabbing the tutorial workshop.</p>

  <p>
    As usual we will use the already built tutorial workshop as a base - the finished code is available on the
    <Code>custom-result</Code>
    branch of the
    <a href="https://github.com/php-school/simple-math">tutorial repository</a>
    . We will start fresh from the
    <Code>custom-interface-check</Code>
    branch for this tutorial, so if you haven't already got it, git clone it and install the dependencies:
  </p>

  <DocTerminal :lines="['cd projects', 'git clone git@github.com:php-school/simple-math.git', 'cd simple-math', 'git checkout custom-interface-check', 'composer install']"></DocTerminal>

  <ContentHeader level="h4" id="result-step-1">1. Create the folder and class</ContentHeader>
  <DocTerminal :lines="['mkdir src/Result', 'touch src/Result/CodingStandardFailure.php']"></DocTerminal>

  <ContentHeader level="h4" id="result-step-2">2. Write the result class</ContentHeader>

  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\SimpleMath\Result;

use PhpSchool\PhpWorkshop\Check\CheckInterface;
use PhpSchool\PhpWorkshop\Result\FailureInterface;

class CodingStandardFailure implements FailureInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $codingStandard;

    /**
     * @var array
     */
    private $errors;

    /**
     * @param string $name
     * @param string $codingStandard
     * @param array $errors
     */
    public function __construct($name, $codingStandard, array $errors)
    {
        $this->name             = $name;
        $this->codingStandard   = $codingStandard;
        $this->errors           = $errors;
    }

    /**
     * @param CheckInterface $check
     * @param $codingStandard
     * @param array $errors
     * @return static
     */
    public static function fromCheckAndOutput(CheckInterface $check, $codingStandard, array $errors)
    {
        return new static($check->getName(), $codingStandard, $errors);
    }

    /**
     * @return string
     */
    public function getCheckName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCodingStandard()
    {
        return $this->codingStandard;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
</pre
    >
  </CodeBlock>

  <p>
    This is a simple class which takes in the check name, the standard used & and an array of violations.
    <Code>getCheckName()</Code>
    should return the name of the check this result represents, this is used when rendering the results to the student by the workshop framework. This is the only method required by the interface
    <Code>PhpSchool\PhpWorkshop\Result\FailureInterface</Code>
    .
  </p>

  <ContentHeader level="h4" id="result-step-3">3. Update the Check</ContentHeader>

  <p>
    We need to update our check to use the new result class, and we need to parse the violations from
    <Code>phpcs</Code>
    . We will only be making changes in the
    <Code>check</Code>
    method and the final method should look like the following:
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php
public function check(ExerciseInterface $exercise, Input $input)
{
    if (!$exercise instanceof Psr2ExerciseCheck) {
        throw new \InvalidArgumentException;
    }

    $standard = $exercise->getStandard();

    if (!in_array($standard, ['PSR1', 'PSR2', 'PEAR'])) {
        throw new \InvalidArgumentException('Standard is not supported');
    }

    $phpCsBinary = __DIR__ . '/../../vendor/bin/phpcs';
    $cmd = sprintf('%s %s --standard=%s --report=json', $phpCsBinary, $input->getArgument('program'), $standard);
    exec($cmd, $output, $exitCode);

    if ($exitCode === 0) {
        return new Success($this->getName());
    }

    $errors = json_decode($output[0], true)['files'][$input->getArgument('program')];
    $errors = array_map(function ($error) {
        return sprintf('Line %d, Column %d: %s', $error['line'], $error['column'], $error['message']);
    }, $errors['messages']);

    return CodingStandardFailure::fromCheckAndOutput($this, $standard, $errors);
}
</pre
    >
  </CodeBlock>

  <ul>
    <li>
      We add the option
      <Code>--report=json</Code>
      to give us the report in json, which makes it easier to parse.
    </li>
    <li>
      We
      <Code>json_decode</Code>
      the first line of output from
      <Code>phpcs</Code>
      .
    </li>
    <li>We grab the violations for the students submission.</li>
    <li>We format each violation in to a string consisting of the line number, column and message.</li>
    <li>Return a instance of our new result class containing the check name, coding standard and an array of violations.</li>
  </ul>

  <Note type="success">That's it! Move on to the next article to build the renderer.</Note>
  <Note type="danger">If you try to run verify the exercise now, the program will crash as it cannot find a renderer for the new result.</Note>
</template>
