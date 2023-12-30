<script setup>
import Code from "../../Code.vue"
import ContentHeader from "../../ContentHeader.vue";
import Terminal from "../../Terminal.vue";
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
    <p>If you don't know what result renderers are, go ahead and read
        <router-link to="/docs/reference/results">this article</router-link> first. We will be continuing on from the previous article,
        let's go ahead and create the renderer.</p>

    <ContentHeader id="renderer-step-1">1. Create the folder and class</ContentHeader>

    <Terminal :lines="['mkdir src/ResultRenderer', 'touch src/ResultRenderer/CodingStandardFailureRenderer.php']"></Terminal>

    <ContentHeader id="renderer-step-2">2. Write the renderer class</ContentHeader>

    <CodeBlock lang="php"><pre>&lt;?php

namespace PhpSchool\SimpleMath\ResultRenderer;

use PhpSchool\PhpWorkshop\ResultRenderer\ResultRendererInterface;
use PhpSchool\PhpWorkshop\ResultRenderer\ResultsRenderer;
use PhpSchool\SimpleMath\Result\CodingStandardFailure;

class CodingStandardFailureRenderer implements ResultRendererInterface
{
    /**
     * @var CodingStandardFailure
     */
    private $failure;

    /**
     * @param CodingStandardFailure $failure
     */
    public function __construct(CodingStandardFailure $failure)
    {
        $this->failure = $failure;
    }

    /**
     * @param ResultsRenderer $renderer
     * @return string
     */
    public function render(ResultsRenderer $renderer)
    {
        $header = sprintf(
            'Coding Standard violations were found using the standard: "%s"',
            $this->failure->getCodingStandard()
        );
        $output = [
            sprintf('  %s', $renderer->style($header, ['bold', 'underline', 'yellow'])),
        ];

        foreach ($this->failure->getErrors() as $error) {
            $output[] = '   * ' . $renderer->style($error, 'red');
        }
        $output[] = '';

        return implode("\n", $output);
    }
}
</pre></CodeBlock>

    <p>This is really simple: the <Code>render(ResultsRenderer $renderer)</Code> just returns a string
        representation of the result, we style the results a little in a bullet pointed list, highlighting them red.
        We also add a title which describes the coding standard used.</p>

    <p>That's basically it - we just need to register the renderer with the application.</p>

    <ContentHeader id="renderer-step-3">3. Register the renderer</ContentHeader>
    <p>Now you need to tell the application about your new result. Open up <Code>app/bootstrap.php</Code> and
        after the application object is created you just call <Code>addResult</Code> with the result class name
        and the result renderer class name. Your final <Code>app/bootstrap.php</Code> file should look something like:</p>

    <CodeBlock lang="php"><pre>&lt;?php

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
use PhpSchool\SimpleMath\Result\CodingStandardFailure;
use PhpSchool\SimpleMath\ResultRenderer\CodingStandardFailureRenderer;

$app = new Application('Simple Math', __DIR__ . '/config.php');

$app->addExercise(Mean::class);
$app->addCheck(Psr2Check::class);
$app->addResult(CodingStandardFailure::class, CodingStandardFailureRenderer::class);

$art = &lt;&lt;&lt;ART
  ∞ ÷ ∑ ×

 PHP SCHOOL
SIMPLE MATH
ART;

$app->setLogo($art);
$app->setFgColour('red');
$app->setBgColour('black');

return $app;
</pre></CodeBlock>

    <ContentHeader id="try-it-out">Try it out!</ContentHeader>

    <p>Run the workshop and select the Mean Average exercise. Verifying a solution which does not pass the
        <Code>PSR2</Code> coding standard will yield the following output:</p>

    <a href="/img/custom-renderer-psr2-fail.png">
        <img src="../../../../../img/cloud/docs/custom-renderer-psr2-fail.png" class="doc-terminal-screen">
    </a>


    <p>You can see the finished, working code on the <Code>custom-result</Code> branch of the
        <a href="https://github.com/php-school/simple-math">tutorial repository</a>.</p>
</template>