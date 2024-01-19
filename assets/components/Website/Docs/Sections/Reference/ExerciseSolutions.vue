<script setup>
import Code from "../../DocCode.vue";
import Note from "../../DocNote.vue";
import ContentHeader from "../../ContentHeader.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
  <p>
    Every CGI & CLI type exercise must have a reference solution. The solution represents a complete working example of
    how to solve the exercise's problem.
  </p>

  <p>The solution is used for a few things in the workshop:</p>
  <ul>
    <li>
      When verifying a student's solution to an exercise, the reference solution is invoked and the output of the two
      are compared.
    </li>
    <li>
      To display to the student as the proposed solution after they have completed the exercise. This is useful if they
      solved the problem in a less optimal albeit working way.
    </li>
  </ul>

  <p>
    A solution is represented by an implementation of
    <Code>PhpSchool\PhpWorkshop\Solution\SolutionInterface</Code>
    .
  </p>
  <p>
    The workshop framework ships with two implementations of
    <Code>PhpSchool\PhpWorkshop\Solution\SolutionInterface</Code>
    to cover most scenarios:
  </p>

  <ul>
    <li>
      <strong>PhpSchool\PhpWorkshop\Solution\SingleFileSolution</strong>
      - When your reference solution is just one file. For example
      <Code>solution.php</Code>
    </li>
    <li>
      <strong>PhpSchool\PhpWorkshop\Solution\DirectorySolution</strong>
      - When your reference solution comprises of multiple files.
    </li>
  </ul>

  <ContentHeader id="single-file-solution">SingleFileSolution</ContentHeader>
  <p>
    This is the default used by
    <Code>PhpSchool\PhpWorkshop\Exercise\AbstractExercise</Code>
    . So if you don't override the
    <Code>getSolution()</Code>
    method, the solution will be a single file named
    <Code>solution.php</Code>
    contained in the directory
    <Code>exercises/exercise-name/solution</Code>
    .
  </p>

  <p>This would look like the following if you were to manually construct it:</p>

  <CodeBlock lang="php">
    <pre>
&lt;?php
require __DIR__ . '/vendor/autoload.php';

use PhpSchool\PhpWorkshop\Solution\SingleFileSolution;

$solution = SingleFileSolution::fromFile('/path/to/workshop/exercises/exercise-name/solution.php');

var_dump($solution->getEntryPoint());
//Outputs: "/path/to/workshop/exercises/exercise-name/solution.php"

var_dump($solution->getBaseDirectory());
//Outputs: "/path/to/workshop/exercises/exercise-name"

var_dump($solution->hasComposerFile());
//Outputs: "false";

$files = $solution->getFiles();

var_dump(count($files));
//Outputs: 1

$file = $files[0];

var_dump($file->getBaseDirectory());
//Outputs: "/path/to/workshop/exercises/exercise-name"

var_dump($file->getRelativePath());
//Outputs: "solution.php"

var_dump($file->__toString());
//Outputs: "/path/to/workshop/exercises/exercise-name/solution.php"

var_dump($file->getContents());
//Outputs: "contents-of-the-file"</pre
    >
  </CodeBlock>

  <ContentHeader id="directory-solution">DirectorySolution</ContentHeader>

  <p>
    It is possible that your solution contains more than one PHP file. Maybe you have some classes separated into
    different files, maybe you also pull in some dependencies via Composer. In either case, you should use
    <Code>PhpSchool\PhpWorkshop\Solution\DirectorySolution</Code>
    .
  </p>

  <p>
    Usage is simple, just pass it the directory and an (optional) entry point. You can also provide an optional list of
    files to exclude, more on that later. The entry point defaults to
    <Code>solution.php</Code>
    . The following is a depiction of a directory structure and the code to encompass the solution:
  </p>
  <CodeBlock lang="php">
    <pre>
/path/to/workshop/exercises/exercise-name/solution
├── SomeClass.php
└── solution.php
</pre
    >
  </CodeBlock>

  <p>
    To return a directory solution when using the
    <Code>PhpSchool\PhpWorkshop\Exercise\AbstractExercise</Code>
    as a base for your exercise you can override the
    <Code>getSolution</Code>
    method with the following:
  </p>
  <CodeBlock lang="php">
    <pre>
&lt;?php

public function getSolution()
{
    return DirectorySolution::fromDirectory('/path/to/workshop/exercises/exercise-name');
}</pre
    >
  </CodeBlock>

  <p>
    This would load any files in the directory given and treat
    <Code>solution.php</Code>
    as the entry point. Constructed manually this might look like:
  </p>
  <CodeBlock lang="php">
    <pre>
&lt;?php
require __DIR__ . '/vendor/autoload.php';

use PhpSchool\PhpWorkshop\Solution\DirectorySolution;

$solution = DirectorySolution::fromDirectory('/path/to/workshop/exercises/exercise-name/solution');

var_dump($solution->getEntryPoint());
//Outputs: "/path/to/workshop/exercises/exercise-name/solution/solution.php"

var_dump($solution->getBaseDirectory());
//Outputs: "/path/to/workshop/exercises/exercise-name/solution"

var_dump($solution->hasComposerFile());
//Outputs: "false";

$files = $solution->getFiles();

var_dump(count($files));
//Outputs: 2

$file1 = $files[0];

var_dump($file1->getBaseDirectory());
//Outputs: "/path/to/workshop/exercises/exercise-name/solution"

var_dump($file1->getRelativePath());
//Outputs: "index.php"

var_dump($file1->__toString());
//Outputs: "/path/to/workshop/exercises/exercise-name/solution/solution.php"

var_dump($file1->getContents());
//Outputs: "contents-of-the-file"

$file2 = $files[1];

var_dump($file2->getBaseDirectory());
//Outputs: "/path/to/workshop/exercises/exercise-name/solution"

var_dump($file2->getRelativePath());
//Outputs: "SomeClass.php"

var_dump($file2->__toString());
//Outputs: "/path/to/workshop/exercises/exercise-name/solution/SomeClass.php"

var_dump($file2->getContents());
//Outputs: "contents-of-the-file"</pre
    >
  </CodeBlock>

  <ContentHeader level="h4" id="different-entry-point">Using a different entry point file</ContentHeader>
  <p>
    If your solution looked like the below where your entry point is named
    <Code>index.php</Code>
    , you can provide the optional third parameter to the static constructor:
    <Code>fromDirectory</Code>
    . It must be the relative path of the file from the solution base directory.
  </p>

  <CodeBlock lang="php">
    <pre>
/path/to/workshop/exercises/exercise-name
├── SomeClass.php
└── index.php
</pre
    >
  </CodeBlock>

  <CodeBlock lang="php">
    <pre>
&lt;?php
require __DIR__ . '/vendor/autoload.php';

use PhpSchool\PhpWorkshop\Solution\DirectorySolution;

$solution = DirectorySolution::fromDirectory('/path/to/workshop/exercises/exercise-name', [], 'index.php');
</pre
    >
  </CodeBlock>

  <Note type="info">
    The convention is for the entry point file to be named
    <Code>solution.php</Code>
    to keep things simple.
  </Note>
  <Note type="danger">
    <Code>DirectorySolution</Code>
    will throw an instance of
    <Code>InvalidArgumentException</Code>
    if the entry point does not exist in the directory given.
  </Note>

  <ContentHeader level="h4" id="excluding-files">Excluding files from the solution directory</ContentHeader>
  <p>
    The method
    <Code>getFiles</Code>
    is used to find all the files in an solution. One use case is to display the contents of the files to the student
    when they have finished an exercise. This way they can compare Notes. Sometimes you may want some files to be
    excluded from this. Perhaps you don't want the
    <Code>composer.lock</Code>
    file to be printed to the terminal as this can be quite long. To exclude some files from the solution, simply
    provide an array of excludes, relative to the base directory:
  </p>
  <CodeBlock lang="php">
    <pre>
&lt;?php
require __DIR__ . '/vendor/autoload.php';

use PhpSchool\PhpWorkshop\Solution\DirectorySolution;

$solution = DirectorySolution::fromDirectory(
    '/path/to/workshop/exercises/exercise-name/solution',
    [
        'composer.lock',
        'vendor'
    ]
);</pre
    >
  </CodeBlock>

  <p>
    It is not actually necessary to exclude
    <Code>composer.lock</Code>
    or
    <Code>vendor</Code>
    as these are automatically appended to the list of excludes when using the static constructor
    <Code>fromDirectory</Code>
    .
  </p>

  <ContentHeader level="h4" id="overriding-excluded-files">Overriding the default excluded files</ContentHeader>
  <p>
    The following files are excluded by default when using the static constructor
    <Code>fromDirectory</Code>
    :
  </p>
  <ul>
    <li>composer.lock</li>
    <li>vendor</li>
  </ul>

  <p>
    If for some reason you do not want to ignore, say
    <Code>composer.lock</Code>
    but still
    <Code>vendor</Code>
    you can use the
    <Code>__construct</Code>
    method which does not have any default values:
  </p>
  <CodeBlock lang="php">
    <pre>
&lt;?php
require __DIR__ . '/vendor/autoload.php';

use PhpSchool\PhpWorkshop\Solution\DirectorySolution;

$solution = new DirectorySolution(
    '/path/to/workshop/exercises/exercise-name/solution',
    'solution.php',
    ['vendor']
);</pre
    >
  </CodeBlock>

  <Note type="info">
    The argument order for
    <Code>__construct</Code>
    and
    <Code>fromDirectory</Code>
    are slightly different.
    <Code>__construct</Code>
    is: $directory, $entryPoint, $excludes.
    <Code>fromDirectory</Code>
    is: $directory, $excludes, $entryPoint.
  </Note>

  <ContentHeader level="h4" id="using-composer-libs">Using Composer Libraries in your solution</ContentHeader>
  <p>
    If you use a library via Composer then you should include the
    <Code>composer.json</Code>
    and
    <Code>composer.lock</Code>
    file in the solution base directory.
    <Code>DirectorySolution</Code>
    will detect the Composer files automatically. If there are Composer files available, the workshop will run a
    <Code>composer install</Code>
    in the solution base directory before invoking the solution.
  </p>

  <Note type="success">This means that you don't need to commit the vendor directory for each reference solution.</Note>

  <ContentHeader id="creating-your-own-solution">Creating your own Exercise Solution Type</ContentHeader>

  <p>
    If the
    <Code>SingleFileSolution</Code>
    or
    <Code>DirectorySolution</Code>
    implementations do not cover your needs, you can create your own by implementing the following interface:
  </p>
  <CodeBlock lang="php">
    <pre>
interface SolutionInterface
{
    /**
     * @return string
     */
    public function getEntryPoint();

    /**
     * @return SolutionFile[]
     */
    public function getFiles();

    /**
     * @return string
     */
    public function getBaseDirectory();

    /**
     * @return bool
     */
    public function hasComposerFile();
}</pre
    >
  </CodeBlock>

  <ContentHeader level="h4-code" id="get-entry-point">getEntryPoint()</ContentHeader>
  <p>
    This method should return the name of the file which should be the entry point to your solution, in absolute form.
  </p>

  <ContentHeader level="h4-code" id="get-files">getFiles()</ContentHeader>
  <p>
    This method should return an array of files. Each file should be represented by an instance of
    <Code>PhpSchool\PhpWorkshop\Solution\SolutionFile</Code>
    .
  </p>

  <ContentHeader level="h4-code" id="get-base-directory">getBaseDirectory()</ContentHeader>
  <p>This should return the absolute path to the directory of the solution.</p>

  <ContentHeader level="h4-code" id="has-composer-file">hasComposerFile()</ContentHeader>
  <p>
    This should return a boolean value depending on whether the solution has a
    <Code>composer.lock</Code>
    file present. If it does, before invoking the solution,
    <Code>composer install</Code>
    will be executed in the solution base directory. This saves you having to bundle the vendor directory in your
    workshop.
  </p>
</template>
