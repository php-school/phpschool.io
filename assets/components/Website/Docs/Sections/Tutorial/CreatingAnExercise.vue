<script setup>
import Code from "../../DocCode.vue";
import Note from "../../DocNote.vue";
import ContentHeader from "../../ContentHeader.vue";
import Terminal from "../../DocTerminal.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
  <p>A workshop is a fairly useless without any exercises, so here we will learn how to create them.</p>
  <Note type="success">
    It may be a good idea for exercises to start off simple and gradually increase in difficulty. You could try to
    explain concepts and build on them with each exercise.
  </Note>

  <ContentHeader id="checklist">Exercise checklist</ContentHeader>
  <ul>
    <li>Decide on a topic to teach.</li>
    <li>Create a working reference solution.</li>
    <li>Create a problem file.</li>
    <li>Write the exercise.</li>
  </ul>

  <p>
    We will decide on a topic of basic PHP operators, more specifically working out the mean average of a given set of
    numbers.
  </p>

  <ContentHeader id="specification">Exercise specification</ContentHeader>

  <ul>
    <li>The student should print out the mean average and nothing else. No new lines or whitespace.</li>
    <li>The numbers passed to the program should be random.</li>
    <li>The amount of numbers passed to the program should be random.</li>
    <li>The numbers should be passed to the program as command line arguments.</li>
  </ul>

  <p>Given this specification we could write a program which would serve as our reference solution.</p>
  <CodeBlock lang="php">
    <pre>
&lt;?php
$count = 0;
for ($i = 1; $i &lt; count($argv); $i++) {
    $count += $argv[$i];
}

$numberCount = count($argv) - 1;
echo $count / $numberCount;</pre
    >
  </CodeBlock>
  <p>
    We should place this file in
    <Code>exercises/mean-average/solution/solution.php</Code>
  </p>

  <Note type="success">
    Reference solutions are known, working programs which pass the exercise. When running a students's solution to an
    exercise, the reference solution is executed and the output compared to the student's.
  </Note>
  <p>
    The next step is to create a problem file. A problem file contains the instructions for the exercise. It should be a
    markdown file. This file is rendered in the Terminal to the student when they select the exercise.
  </p>

  <ContentHeader id="tips-for-a-problem-file">Tips for a good problem file</ContentHeader>
  <ul>
    <li>Provide a solid description of the problem.</li>
    <li>Provide some sample code which may need to be modified.</li>
    <li>Provide hints and tips.</li>
    <li>
      Provide links to the PHP documentation and good articles from reputable sources regarding key areas of the
      problem.
    </li>
  </ul>

  <p>Our problem file might look like the following.</p>
  <CodeBlock lang="md">
    <pre>
Write a program that accepts one or more numbers as command-line arguments and prints the mean average of those numbers to the console (stdout).

----------------------------------------------------------------------
## HINTS

You can access command-line arguments via the global `$argv` array.

To get started, write a program that simply contains:

```php
var_dump($argv);
```

Run it with `php program.php` and some numbers as arguments. e.g:

```sh
$ php program.php 1 2 3
```

In which case the output would be an array looking something like:

```php
array(4) {
[0] =>
string(7) "program.php"
[1] =>
string(1) "1"
[2] =>
string(1) "2"
[3] =>
string(1) "3"
}
```

You'll need to think about how to loop through the number of arguments so you can output just their mean average. The first element of the `$argv` array is always the name of your script. eg `program.php`, so you need to start at the 2nd element (index 1), adding each item to the total until you reach the end of the array. You will then need to work out the average based on the amount of arguments given to you.

You can read how to work out an average here:
  [https://www.mathsisfun.com/mean.html]()

Also be aware that all elements of `$argv` are strings and you may need to *coerce* them into numbers. You can do this by prefixing the property with a cast `(int)` or just adding them. PHP will coerce it for you.

`{appname}` will be supplying arguments to your program when you run `{appname} verify program.php` so you don't need to supply them yourself. To test your program without verifying it, you can invoke it with `{appname} run program.php`. When you use `run`, you are invoking the test environment that `{appname}` sets up for each exercise.

----------------------------------------------------------------------</pre
    >
  </CodeBlock>

  <Note type="info">
    Any instances of
    <Code>{appname}</Code>
    will be replaced with the actual application name, this will most likely be the configuration you set when creating
    your workshop as this is inferred from the command the student executed to run the workshop.
  </Note>
  <p>
    Drop this file in
    <Code>exercises/mean-average/problem/problem.md</Code>
    .
  </p>

  <ContentHeader id="write-the-exercise">Write the exercise</ContentHeader>

  <p>Now we write the code, there is not much to it, this is a simple exercise!</p>

  <CodeBlock lang="php">
    <pre>
&lt;?php

namespace PhpSchool\SimpleMath\Exercise;

use PhpSchool\PhpWorkshop\Exercise\AbstractExercise;
use PhpSchool\PhpWorkshop\Exercise\CliExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;

class Mean extends AbstractExercise implements ExerciseInterface, CliExercise
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
     * @return string[][]
     */
    public function getArgs()
    {
        $numArgs = rand(1, 10);

        $args = [];
        for ($i = 0; $i &lt; $numArgs; $i ++) {
            $args[] = rand(0, 100);
        }

        return [$args];
    }

    /**
     * @return ExerciseType
     */
    public function getType()
    {
        return ExerciseType::CLI();
    }
}
</pre
    >
  </CodeBlock>
  <p>
    Place the above in
    <Code>src/Exercise/Mean.php</Code>
    .
  </p>
  <p>Now lets break this down.</p>
  <p>
    This class represents our exercise, it describes how the programs will be executed, the student's and our reference
    solution.
  </p>

  <ContentHeader level="h4" id="abstract-exercise">AbstractExercise</ContentHeader>
  <p>
    The
    <Code>AbstractExercise</Code>
    class implements a few interesting methods for us. Mainly
    <Code>getSolution</Code>
    and
    <Code>getProblem</Code>
    . These methods are responsible for locating your solution and problem files. By default they take your exercise's
    name, normalise it (remove anything that is not A-Za-z or a dash, lowercase and replace spaces with dashes) and look
    in the
    <Code>exercises/&lt;normalised-name&gt;/solution</Code>
    and
    <Code>exercises/&lt;normalised-name&gt;/problem</Code>
    folders for files named
    <Code>solution.php</Code>
    and
    <Code>problem.md</Code>
    respectively. There maybe be cases when you need to override these methods, and in that case you probably don't need
    to extend from
    <Code>AbstractExercise</Code>
    .
  </p>
  <Note type="info">
    You may need to override the methods
    <Code>getSolution</Code>
    and
    <Code>getProblem</Code>
    if you want to organise your problems and solutions in a different structure. We would advise against this in the
    name of consistency but if you have a good enough reason then the option is there. There may also be the case that
    your solution is not simply one file. Jump over to
    <router-link to="/docs/reference/exercise-solutions">Exercise Solutions</router-link>
    to learn more, if that is the case.
  </Note>

  <ContentHeader level="h4" id="exercise-types">Exercise Type</ContentHeader>
  <p>
    Each exercise must have a type, there are currently two types of exercise:
    <Code>CGI</Code>
    ,
    <Code>CLI</Code>
    &
    <Code>CUSTOM</Code>
    . Head over to
    <router-link to="/docs/reference/exercise-types">Exercise Types</router-link>
    to learn more. We are currently building a
    <Code>CLI</Code>
    type exercise, this means our reference solution and the student's solution programs will be invoked using the PHP
    CLI binary. The arguments will come from our exercise class. We inform the workshop of our exercise type by
    returning an instance of
    <Code>ExerciseType</Code>
    from the
    <Code>getType</Code>
    method.
    <Code>ExerciseType</Code>
    is an ENUM. In conjunction with this, our exercise should implement the respective interface. For
    <Code>CLI</Code>
    type exercises this is
    <Code>CliExercise</Code>
    .
  </p>

  <ContentHeader level="h4" id="cli-exercise">CliExercise</ContentHeader>
  <p>
    This interface defines one method:
    <Code>getArgs</Code>
    . This method should return an array of arrays containing string arguments which will be passed to our reference
    solution and the student's solution at runtime. Each set of arguments will be sent to the solution. So you could
    essentially run the student's solution as many times as you wanted with different arguments. This method can return
    random records and random numbers of arguments so that each time the student runs the verification process they
    receive different arguments. This makes sure the solution is robust.
  </p>
  <Note type="success">
    Try passing arguments which will test the boundaries of the student's solution, for example using minimum and
    maximum values and using random values on each invocation.
  </Note>
  <Note type="info">
    Do Note that although your implementation of
    <Code>getArgs</Code>
    may return random arguments, your reference solution and the student's solution will always receive the same
    arguments as the
    <Code>getArgs</Code>
    method is only called once.
  </Note>
  <p>
    Our exercise simply returns one set of random number of arguments between 0 and 10, each being a random number
    between 0 and 100.
  </p>

  <ContentHeader level="h4" id="name-and-description">Name and description</ContentHeader>
  <p>
    The remaining methods to implement are
    <Code>getName</Code>
    and
    <Code>getDescription</Code>
    .
    <Code>getName</Code>
    is the name of the exercise to be displayed in the menu and
    <Code>getDescription</Code>
    is a short description of the exercise. This is not actually used anywhere yet but is useful when glancing through
    the code.
  </p>

  <ContentHeader id="registering-exercise">Registering the exercise and adding a factory</ContentHeader>

  <p>
    Internally, the workshop application uses a
    <a target="_blank" href="https://en.wikipedia.org/wiki/Dependency_Injection">dependency injection container</a>
    . This allows you to request other services from the application and replace services with your own implementations.
    In order for the application to locate your exercise, you need to register it with the application and also provide
    a factory for it. We use the
    <a target="_blank" href="http://php-di.org">PHP-DI</a>
    package for dependency injection.
  </p>
  <p>
    First, lets create a factory for our exercise. Open up
    <Code>app/config.php</Code>
    .
  </p>

  <CodeBlock lang="php">
    <pre>
return [
    Mean::class => \DI\object(),
];</pre
    >
  </CodeBlock>
  <p>
    The file
    <Code>app/config.php</Code>
    should return an array of service definitions for the container. The key being the name of the service and the value
    the actual factory. For the case of exercises the service name should
    <strong>always</strong>
    be the class name.
    <Code>\DI\object()</Code>
    is a helper function to create a factory which will simply run
    <Code>new $yourClassName</Code>
    when asking for the service from the container.
  </p>

  <Note type="success">
    See the section on
    <router-link to="/docs/reference/container">The Container</router-link>
    for more information on service definitions.
  </Note>

  <p>
    You are almost done! we have registered the factory which tells the application how to create your exercise. We just
    need to make it aware of your exercise. We do this in
    <Code>app/bootstrap.php</Code>
    . After the
    <Code>Application</Code>
    object is created you just call
    <Code>addExercise</Code>
    with the name of your exercise class. Your final
    <Code>app/bootstrap.php</Code>
    file should look something like the following:
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
use PhpSchool\SimpleMath\Exercise\Mean;

$app = new Application('Simple Math', __DIR__ . '/config.php');

$app->addExercise(Mean::class);

$art = &lt;&lt;&lt;ART
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

  <p>That's it! You should now see your exercise in the menu when you run the app.</p>

  <Terminal :lines="['php bin/simple-math']"></Terminal>

  <a href="/img/first-exercise.png">
    <img src="../../../../../img/cloud/docs/first-exercise.png" class="doc-Terminal-screen" id="first-exercise" />
  </a>
  <p>
    You can compare your workshop against
    <a href="https://github.com/php-school/simple-math">https://github.com/php-school/simple-math</a>
    . This is a working copy of the tutorial workshop.
  </p>
</template>
