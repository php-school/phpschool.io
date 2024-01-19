<script setup>
import Code from "../../DocCode.vue";
import Note from "../../DocNote.vue";
import ContentHeader from "../../ContentHeader.vue";
import ExerciseType from "../../ExerciseType.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
  <p>
    The exercise type indicated how the exercise should be verified, each exercise must have a type. There are three
    types of exercise types as of writing
    <a href="#cli">CLI</a>
    ,
    <a href="#cgi">CGI</a>
    &
    <a href="#custom">CUSTOM</a>
    . In this section we will detail what the verification process for each exercise type is.
  </p>

  <p>When selecting your exercise type you must return the valid type and implement the respective interface.</p>

  <ContentHeader id="cli">CLI</ContentHeader>

  <p>
    You choose the CLI type if you want the user to interact with the
    <Code>$argv</Code>
    and
    <Code>$argc</Code>
    super globals.
  </p>

  <ExerciseType
    :type="'ExerciseType::CLI()'"
    :interface-to-implement="'PhpSchool\PhpWorkshop\Exercise\CliExercise'"
    :methods="'getArgs'"
  ></ExerciseType>

  <ContentHeader level="h4" id="cli-get-args">Implementing getArgs</ContentHeader>
  <p>
    <Code>getArgs</Code>
    should return an array of string arrays. These are the arguments to be passed to the PHP CLI executable. The number
    of arguments which can be returned is not limited. All arguments should be strings. If multiple sets of arguments
    are specified then the program will be executed each time with those arguments.
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\MyWorkshop\Exercise;

use PhpSchool\PhpWorkshop\Exercise\AbstractExercise;
use PhpSchool\PhpWorkshop\Exercise\CliExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;

class MyExercise extends AbstractExercise implements ExerciseInterface, CliExercise
{
     ...snip

    /**
     * @return array
     */
    public function getArgs()
    {
        return [
            ['arg1', 'arg2', 'arg3'], //first execution
            ['run2-arg1', 'run2-arg2', 'run2-arg3'] //second execution
        ];
    }

    /**
     * @return ExerciseType
     */
    public function getType()
    {
        return ExerciseType::CLI();
    }
}</pre
    >
  </CodeBlock>
  <p>This exercise would cause PHP to be invoked like so:</p>
  <CodeBlock lang="sh">
    <pre>php solution.php 'arg1' 'arg2' 'arg3'</pre>
  </CodeBlock>
  <p>And then:</p>
  <CodeBlock lang="sh">
    <pre>php solution.php 'run2-arg1' 'run2-arg2' 'run2-arg3'</pre>
  </CodeBlock>

  <ContentHeader id="cgi">CGI</ContentHeader>

  <p>
    You choose the CGI type if you want the user to interact with the
    <Code>$_GET</Code>
    ,
    <Code>$_POST</Code>
    ,
    <Code>$_SERVER</Code>
    , etc super globals.
  </p>
  <p>This exercise type allows to simulate a real HTTP request to a PHP script.</p>

  <ExerciseType
    :type="'ExerciseType::CGI()'"
    :interface-to-implement="'PhpSchool\PhpWorkshop\Exercise\CgiExercise'"
    :methods="'getRequests'"
  ></ExerciseType>

  <ContentHeader level="h4" id="cgi-get-requests">Implementing getRequests</ContentHeader>

  <p>
    <Code>getRequests</Code>
    should return an array of PSR-7 Requests. The number of requests is not limited. Check
    <a target="_blank" href="http://www.php-fig.org/psr/psr-7/">here</a>
    for more information about PSR-7. The method is currently type hinted to return an array of the
    <a target="_blank" href="http://www.php-fig.org/psr/psr-7/#3-2-psr-http-message-requestinterface">
      Psr\Http\Message\RequestInterface
    </a>
    interface. You will need to use a library which implements the PSR-7 standard. Currently
    <a target="_blank" href="https://github.com/zendframework/zend-diactoros">zendframework/zend-diactoros</a>
    is shipped with
    <Code>php-school/php-workshop</Code>
    so you can just use that, or feel free to pull in a different implementation.
  </p>

  <ContentHeader level="h5" id="cgi-get">Simulating a GET request</ContentHeader>
  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\MyWorkshop\Exercise;

use PhpSchool\PhpWorkshop\Exercise\AbstractExercise;
use PhpSchool\PhpWorkshop\Exercise\CgiExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Request;

class MyExercise extends AbstractExercise implements ExerciseInterface, CgiExercise
{
    ...snip

    **
     * @return RequestInterface[]
     */
    public function getRequests()
    {
        return [
            (new Request('http://www.my-library.com?genre=punk&limit=50'))
                ->withMethod('GET')

        ];
    }

    /**
     * @return ExerciseType
     */
    public function getType()
    {
        return ExerciseType::CGI();
    }
}
</pre
    >
  </CodeBlock>
  <p>The request above will result in PHP super globals looking something like this:</p>
  <CodeBlock lang="php">
    <pre>
//$_SERVER
array(13) {
    ["HTTP_HOST"] => string(18) "www.my-library.com"
    ["SCRIPT_FILENAME"] => string(57) "/root/path/your-workshop/get-solution.php"
    ["PWD"] => string(40) "/root/path/your-workshop"
    ["REDIRECT_STATUS"] => string(3) "302"
    ["SHLVL"] => string(1) "1"
    ["CONTENT_LENGTH"] => string(1) "0"
    ["QUERY_STRING"] => string(19) "genre=punk&limit=50"
    ["REQUEST_METHOD"] => string(3) "GET"
    ["_"] => string(48) "/usr/local/bin/php-cgi"
    ["__CF_USER_TEXT_ENCODING"] => string(13) "0x1F5:0x0:0x0"
    ["PHP_SELF"] => string(0) ""
    ["REQUEST_TIME_FLOAT"] => float(1458634262.4929)
    ["REQUEST_TIME"] => int(1458634262)
}

//$_GET
array(2) {
    ["genre"] => string(4) "punk"
    ["limit"] => string(2) "50"
}</pre
    >
  </CodeBlock>
  <ContentHeader level="h5" id="cgi-post">Simulating a POST request</ContentHeader>
  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\MyWorkshop\Exercise;

use PhpSchool\PhpWorkshop\Exercise\AbstractExercise;
use PhpSchool\PhpWorkshop\Exercise\CgiExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Request;

class MyExercise extends AbstractExercise implements ExerciseInterface, CgiExercise
{
    ...snip

    /**
     * @return RequestInterface[]
     */
    public function getRequests()
    {
        $request1 = (new Request('http://www.my-library.com?genre=punk&limit=50'))
            ->withMethod('POST')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded');

        $request1->getBody()->write(urlencode(['genre' => 'punk', 'limit' => 200]));
        return [$request1];
    }

    /**
     * @return ExerciseType
     */
    public function getType()
    {
        return ExerciseType::CGI();
    }
}</pre
    >
  </CodeBlock>
  <p>The request above will result in PHP super globals looking something like this:</p>
  <CodeBlock lang="php">
    <pre>
//$_SERVER
 array(14) {
    ["HTTP_HOST"] => string(18) "www.my-library.com"
    ["SCRIPT_FILENAME"] => string(58) "/root/path/your-workshop/get-post-solution.php"
    ["HTTP_CONTENT_TYPE"] => string(33) "application/x-www-form-urlencoded"
    ["PWD"] => string(40) "/root/path/your-workshop"
    ["REDIRECT_STATUS"] => string(3) "302"
    ["SHLVL"] => string(1) "1"
    ["CONTENT_LENGTH"] => string(2) "17"
    ["CONTENT_TYPE"] => string(33) "application/x-www-form-urlencoded"
    ["REQUEST_METHOD"] => string(4) "POST"
    ["_"] => string(48) "/usr/local/bin/php-cgi"
    ["__CF_USER_TEXT_ENCODING"] => string(13) "0x1F5:0x0:0x0"
    ["PHP_SELF"] => string(0) ""
    ["REQUEST_TIME_FLOAT"] => double(1458845731.5657)
    ["REQUEST_TIME"] => int(1458845731)
 }

 //$_POST
 array(2) {
    ["genre"] => string(4) "punk"
    ["limit"] => string(2) "50"
 }</pre
    >
  </CodeBlock>
  <ContentHeader id="how-does-this-work">How does this work?</ContentHeader>
  <p>
    We use the
    <Code>php-cgi</Code>
    binary to setup the PHP environment. The
    <Code>php-cgi</Code>
    binary uses environment variables and standard input to configure itself, we export these variables based on the
    information you provide in
    <Code>getRequests</Code>
    .
  </p>

  <Note type="success">
    <ul>
      <li>
        You can return multiple request from
        <Code>getRequests</Code>
        .
      </li>
      <li>Each request can have a different method.</li>
      <li>Each request can have any amount of headers as you need.</li>
    </ul>
  </Note>

  <ContentHeader id="custom">CUSTOM</ContentHeader>

  <p>
    You choose the CUSTOM type if you want the user to perform some arbitrary task that does not involve writing PHP
    code. For example the exercise could be to ask the user to download and install a program and then we could check
    that it is running.
  </p>
  <p>This exercise type allows to simulate a real HTTP request to a PHP script.</p>

  <ExerciseType
    :type="'ExerciseType::CUSTOM()'"
    :interface-to-implement="'PhpSchool\PhpWorkshop\Exercise\CustomVerifyingExercise'"
    :methods="'verify'"
  ></ExerciseType>

  <ContentHeader level="h4" id="custom-verify">Implementing verify</ContentHeader>
  <p>
    This is where you perform your verification. The method needs to return an instance of
    <Code>PhpSchool\PhpWorkshop\Result\ResultInterface</Code>
    . Depending on whether you want a success or failure to be recorded it will be an instance of
    <Code>PhpSchool\PhpWorkshop\Result\SuccessInterface</Code>
    or
    <Code>PhpSchool\PhpWorkshop\Result\FailureInterface</Code>
    . Learn about results
    <router-link to="/docs/reference/result">here</router-link>
    .
  </p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\MyWorkshop\Exercise;

use PhpSchool\PhpWorkshop\Exercise\AbstractExercise;
use PhpSchool\PhpWorkshop\Exercise\CustomVerifyingExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;
use PhpSchool\PhpWorkshop\Result\Success;
use PhpSchool\PhpWorkshop\Result\Failure;

class MyExercise extends AbstractExercise implements ExerciseInterface, CustomVerifyingExercise
{
    ...snip

    /**
     * @return ResultInterface
     */
    public function verify()
    {
        if (//program is running) {
            return new Success('My Program Check');
        }
        return new Failure('My Program Check', 'Program is running!');
    }

    /**
     * @return ExerciseType
     */
    public function getType()
    {
        return ExerciseType::CGI();
    }
}</pre
    >
  </CodeBlock>

  <p>
    When a student runs the verify command your verify method will be invoked and the results will be displayed to the
    student!
  </p>
</template>
