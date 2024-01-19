<script setup>
import Terminal from "../../DocTerminal.vue";
import Code from "../../DocCode.vue";
import ContentHeader from "../../ContentHeader.vue";
import Note from "../../DocNote.vue";
</script>
<template>
  <p>
    In this tutorial we will be building a simple workshop which is available
    <a href="https://github.com/php-school/simple-math">here on GitHub</a>
    as a reference, if you have any problems, you can double check your work.
  </p>

  <p>
    In order to create a workshop the first thing you will need to do is to setup a new project and configure the
    dependencies and wire it all together. Luckily for you we have created a starter kit which does this all for you,
    prompting you along the way. Simply execute the following command in your shell.
  </p>

  <Terminal :lines="['composer create-project php-school/workshop-starter-kit <your-workshop-name>']"></Terminal>

  <p>
    Where
    <Code>&lt;your-workshop-name&gt;</Code>
    is the name of the directory you want to setup your workshop in. For this tutorial we will create a workshop
    teaching some basic math concepts so we will use the name
    <code>simple-math</code>
    . You will be prompted for a few things:
  </p>

  <ContentHeader id="composer-package-name">Composer package name</ContentHeader>
  <p>
    The name for your workshop to be put in the
    <code>composer.json</code>
    file. This should follow the pattern
    <Code>vendor/package</Code>
    . In the case of our flagship workshop
    <a target="_blank" href="https://github.com/php-school/learn-you-php">Learn You PHP!</a>
    this is
    <Code>php-school/learn-you-php</Code>
    . Name your package
    <Code>php-school/simple-math</Code>
    .
  </p>
  <p>
    More info on the
    <Code>name</Code>
    property
    <a target="_blank" href="https://getcomposer.org/doc/04-schema.md#name">
      can be found in the composer documentation
    </a>
    .
  </p>

  <ContentHeader id="composer-package-description">Composer package description</ContentHeader>
  <p>
    A short description of your workshop which will be added to the
    <Code>composer.json</Code>
    file. Add a short description here, something like:
    <Code>Learn the basics of math with PHP!</Code>
  </p>
  <p>
    More info on the
    <Code>description</Code>
    property
    <a target="_blank" href="https://getcomposer.org/doc/04-schema.md#description">
      can be found in the composer documentation
    </a>
    .
  </p>

  <ContentHeader id="namespace">PHP Namespace for the source Code</ContentHeader>
  <p>
    The namespace which will be used in the project so it can be added to the autoload configuration in the
    <Code>composer.json</Code>
    file. In the case of
    <a target="_blank" href="https://github.com/php-school/learn-you-php">Learn You PHP!</a>
    this is
    <Code>PhpSchool\LearnYouPhp</Code>
    . We will use
    <Code>PhpSchool\SimpleMath</Code>
    .
  </p>

  <p>
    More info on the
    <Code>autoload</Code>
    property
    <a target="_blank" href="https://getcomposer.org/doc/04-schema.md#psr-4">
      can be found in the composer documentation
    </a>
    .
  </p>

  <ContentHeader id="binary">Workshop binary</ContentHeader>
  <p>
    This is the actual command students will execute to run your workshop. In the case of
    <a target="_blank" href="https://github.com/php-school/learn-you-php">Learn You PHP!</a>
    this is
    <Code>learnyouphp</Code>
    . You should set this as
    <Code>simple-math</Code>
    .
  </p>

  <br />
  <hr />
  <br />
  <p>
    After you have entered the required information the workshop dependencies will be downloaded and the autoload files
    will be generated.
  </p>

  <p>Generate the autoload information for our Namespace:</p>
  <Terminal :lines="['composer dump-autoload']"></Terminal>

  <Note type="success">
    Assuming everything went well and your are in the root of your new workshop you should be able to run the workshop!
  </Note>
  <Terminal :lines="['php bin/<binary-name>']"></Terminal>

  <p>
    Where
    <Code>&lt;binary-name&gt;</Code>
    is the name your previously entered during the
    <Code>create-project</Code>
    command. In our case:
  </p>
  <Terminal :lines="['php bin/simple-math']"></Terminal>

  <a href="/img/post-install-workshop.png">
    <img
      src="../../../../../img/cloud/docs/post-install-workshop.png"
      class="doc-terminal-screen"
      id="post-install-workshop"
    />
  </a>

  <Note type="danger">
    If you don't see the menu above, then something went wrong. Get in touch with us and bring any helpful information
    such as error messages with you!
  </Note>
</template>
