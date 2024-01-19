<script setup>
import Code from "../../DocCode.vue";
import ContentHeader from "../../ContentHeader.vue";
import ResultRendererMappings from "../../ResultRendererMappings.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
  <p>
    After a student's solution has been verified, the result set is rendered to the console. The result set is made up of several individual results. Verification is deemed to have failed if any one
    of those results is a failure. Each result represents a different thing, for example, each check will likely inject a result in to the result set. The output verification will be a single result,
    the parsing of the file will be a single result, and so on.
  </p>

  <p>Each Result class has an associated renderer, the renderers job is to take the information from the result and render in to the console.</p>

  <p>
    Results are what
    <router-link to="/docs/reference/exercise-checks">Exercise Checks</router-link>
    should return and inject. You will learn more about how to actually use the results in your checks in a
    <router-link to="/docs/reference/exercise-checks">later article</router-link>
    .
  </p>

  <ContentHeader id="result-set">The Result Set</ContentHeader>
  <p>
    The result set is an instance of
    <Code>PhpSchool\PhpWorkshop\ResultAggregator</Code>
    and results are added to it with
    <Code>add(ResultInterface $result)</Code>
    . Note the interface
    <Code>PhpSchool\PhpWorkshop\Result\ResultInterface</Code>
    . Every result must implement this interface.
  </p>

  <ContentHeader id="success-or-failure">Success or Failure?</ContentHeader>
  <p>
    So, how do we know if a result is a success or failure? Well there are two other interfaces, extending from
    <Code>ResultInterface</Code>
    , which are:
  </p>

  <ul>
    <li><Code>PhpSchool\PhpWorkshop\Result\SuccessInterface</Code></li>
    <li><Code>PhpSchool\PhpWorkshop\Result\FailureInterface</Code></li>
  </ul>

  <p>Both of these interfaces add no extra methods, they are purely for determining whether a result is considered a success or failure.</p>

  <ContentHeader id="result-interface">Result Interface</ContentHeader>
  <p>
    The interface for
    <Code>ResultInterface</Code>
    is very simple:
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

interface ResultInterface
{
    /**
     * @return string
     */
    public function getCheckName();
}</pre
    >
  </CodeBlock>

  <p>This method should just return the name of the check associated with this result. This is used when rendering the result to the console.</p>

  <ContentHeader id="implementations">Implementations</ContentHeader>
  <p>
    There are default implementations for
    <Code>SuccessInterface</Code>
    &
    <Code>FailureInterface</Code>
    for you to use in your checks. If you need something more bespoke to render the failure of your check, you should
    <router-link to="/docs/reference/creating-custom-results">create your own</router-link>
    .
  </p>

  <ContentHeader level="h4-code" id="success">PhpSchool\PhpWorkshop\Result\Success</ContentHeader>
  <p>As you saw from the interface, the only required piece of information is the check name. So construction would look like the following.</p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

use PhpSchool\PhpWorkshop\Result\Success;

$success = new Success('My Check');
</pre
    >
  </CodeBlock>

  <p>
    If you are within a Check (eg
    <Code>$this</Code>
    refers to
    <Code>PhpSchool\PhpWorkshop\Check\CheckInterface</Code>
    ) then you can use the static constructor
    <Code>fromCheck(CheckInterface $check)</Code>
    which just pulls the check name from the actual check for convenience.
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\PhpWorkshop\Check;

use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Result\Success;

class MyCheck implements SimpleCheckInterface
{
    ...snip

    public function check(ExerciseInterface $exercise, $fileName)
    {
        return Success::fromCheck($this);
    }
}
</pre
    >
  </CodeBlock>

  <ContentHeader level="h4-code" id="failure">PhpSchool\PhpWorkshop\Result\Failure</ContentHeader>
  <p>
    The default implementation of
    <Code>FailureInterface</Code>
    needs one more piece of information other than the check name: the reason for the failure. Construction is fairly similar:
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

use PhpSchool\PhpWorkshop\Result\Failure;

//constructor
$failure = new Failure('My Check', 'Something went wrong!');

//static constructor
$failure = Failure::fromNameAndReason('My Check', 'Something went wrong!');

//static constructor with check
$myCheck = new MyCheck;
$failure = Failure::fromCheckAndReason($check, 'Something went wrong!');
</pre
    >
  </CodeBlock>

  <ContentHeader id="failure-implementations">Bundled Failure Implementations</ContentHeader>
  <p>
    There are a number of
    <Code>FailureInterface</Code>
    implementations bundled with the framework for some of the other checks:
  </p>

  <ul>
    <li><Code>PhpSchool\PhpWorkshop\Result\Cgi\GenericFailure</Code></li>
    <li><Code>PhpSchool\PhpWorkshop\Result\Cgi\RequestFailure</Code></li>
    <li><Code>PhpSchool\PhpWorkshop\Result\Cli\GenericFailure</Code></li>
    <li><Code>PhpSchool\PhpWorkshop\Result\Cli\RequestFailure</Code></li>
    <li>
      <Code>PhpSchool\PhpWorkshop\Result\FunctionRequirementsFailure</Code>
    </li>
    <li><Code>PhpSchool\PhpWorkshop\Result\ComparisonFailure</Code></li>
  </ul>

  <ContentHeader id="custom-results">Custom Results</ContentHeader>
  <p>
    When you want to report information that is not simple a message, you will need to create your own result class. If you would build a check that verifies the contents of a database, you may want
    to provide a list of missing records as an array instead of just a message. You would then write a renderer that may render each row as a new line with a bullet point preceding it. Learn how to
    create your own checks
    <router-link to="/docs/reference/creating-custom-results">in a later article</router-link>
    .
  </p>

  <ContentHeader id="result-renderer-interface">Result Renderers</ContentHeader>
  <p>
    Each result renderer must implement the interface
    <Code>PhpSchool\PhpWorkshop\ResultRenderer\ResultRendererInterface</Code>
    which looks like below.
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\PhpWorkshop\ResultRenderer;

use PhpSchool\PhpWorkshop\Result\ResultInterface;

interface ResultRendererInterface
{

    /**
     * @param ResultsRenderer $renderer
     * @return string
     */
    public function render(ResultsRenderer $renderer);
}
</pre
    >
  </CodeBlock>

  <p>
    <Code>PhpSchool\PhpWorkshop\ResultAggregator</Code>
    is rendered by
    <Code>PhpSchool\PhpWorkshop\ResultRenderer\ResultsRenderer</Code>
    . It loops each result, passing the result to
    <Code>PhpSchool\PhpWorkshop\Factory\ResultRendererFactory</Code>
    which returns the correct renderer. The
    <Code>render</Code>
    method is called and then the output of each is written to the console.
  </p>

  <p>
    When each renderer is created, it is passed the
    <Code>ResultInterface</Code>
    as the first constructor argument.
    <Code>render</Code>
    is called with an instance of
    <Code>PhpSchool\PhpWorkshop\ResultRenderer\ResultsRenderer</Code>
    and it should return a string representation of the
    <Code>ResultInterface</Code>
    instance it was constructed with.
  </p>

  <p>
    <Code>PhpSchool\PhpWorkshop\ResultRenderer\ResultsRenderer</Code>
    has some helper methods on it for rendering styling:
  </p>

  <ul>
    <li>
      <Code>style($string, $colourOrStyle)</Code>
      - Use to style a string, eg.
      <Code>bold</Code>
      ,
      <Code>green</Code>
      . It accepts an array of styles or one style as a string
    </li>
    <li>
      <Code>lineBreak()</Code>
      - Use to render a line break, to separate content.
    </li>
    <li>
      <Code>center()</Code>
      - Pad a string according to the terminal width so it displays in the center
    </li>
  </ul>

  <ContentHeader id="result-renderer-mappings">Result Renderer Mappings</ContentHeader>
  <p>
    The workshop framework picks a result renderer based on the mappings in
    <Code>PhpSchool\PhpWorkshop\Factory\ResultRendererFactory</Code>
    .
  </p>

  <ResultRendererMappings
    :results="[
      ['PhpSchool\\PhpWorkshop\\Result\\Cgi\\GenericFailure', 'PhpSchool\\PhpWorkshop\\ResultRenderer\\FailureRenderer'],
      ['PhpSchool\\PhpWorkshop\\Result\\Cgi\\RequestFailure', 'PhpSchool\\PhpWorkshop\\ResultRenderer\\Cgi\\RequestFailureRenderer'],
      ['PhpSchool\\PhpWorkshop\\Result\\Cli\\GenericFailure', 'PhpSchool\\PhpWorkshop\\ResultRenderer\\FailureRenderer'],
      ['PhpSchool\\PhpWorkshop\\Result\\Cli\\RequestFailure', 'PhpSchool\\PhpWorkshop\\ResultRenderer\\Cli\\RequestFailureRenderer'],
      ['PhpSchool\\PhpWorkshop\\Result\\ComparisonFailure', 'PhpSchool\\PhpWorkshop\\ResultRenderer\\ComparisonFailureRenderer'],
      ['PhpSchool\\PhpWorkshop\\Result\\FunctionRequirementsFailure', 'PhpSchool\\PhpWorkshop\\ResultRenderer\\FunctionRequirementsFailureRenderer'],
      ['PhpSchool\\PhpWorkshop\\Result\\Failure', 'PhpSchool\\PhpWorkshop\\ResultRenderer\\FailureRenderer'],
    ]"
  ></ResultRendererMappings>

  <p>
    If you create a new implementation of
    <Code>FailureInterface</Code>
    you will need to map it to an existing renderer, or most likely you will need to write a custom renderer, and map it to that.
  </p>

  <ContentHeader id="summary">Summary</ContentHeader>
  <p>
    The whole process may sound complicated, however, this is not true. To summarise, your check should return a result. The result should be mapped to a renderer. The results are rendered by the
    framework. It will pick the correct renderer based on the mapping.
  </p>

  <ContentHeader id="create-a-result">Create a custom result</ContentHeader>
  <p>
    In the next set of articles we will learn about and build a check. Once the check is complete, we will build a custom result and result renderer for it, you can jump
    <router-link to="/docs/reference/creating-custom-results">straight there if you want</router-link>
    .
  </p>
</template>
