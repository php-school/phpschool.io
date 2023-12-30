<script setup>
import Code from "../../Code.vue"
import CodeBlock from "../../CodeBlock.vue";
</script>
<template>
    <p>You can alter the colours, logo and text of your workshop very easily. Look in <Code>app/bootstrap.php</Code> and you should see the following code.</p>

    <CodeBlock lang="php"><pre>$app->setLogo($art);
$app->setFgColour('green');
$app->setBgColour('black');</pre></codeBlock>

    <p>You can modify any of these settings. Available colours are black, red, green, yellow, blue, magenta, cyan, white. <Code>setLogo</Code> takes a <Code>string</Code> and can be used display ascii art!</p>
    <p>You can set the title of your workshop which will be displayed on the terminal by changing the <Code>null</Code> argument to <Code>Application</Code> to a title of your choice. For example:</p>

    <CodeBlock lang="php"><pre>$app = new Application('My Workshop', __DIR__ . '/config.php');</pre></CodeBlock>

    <p>We will customise the theme a little to be more representative of our subject:</p>

    <CodeBlock lang="php"><pre>$art = &lt;&lt;&lt;ART
  ∞ ÷ ∑ ×

 PHP SCHOOL
SIMPLE MATH
ART;

$app->setLogo($art);
$app->setFgColour('red');
$app->setBgColour('black');</pre></CodeBlock>

    <a href="/img/themed-workshop.png">
        <img src="../../../../../img/cloud/docs/themed-workshop.png" class="doc-terminal-screen" id="themed-workshop">
    </a>
</template>