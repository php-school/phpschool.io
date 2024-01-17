<script setup>
import Code from '../../DocCode.vue'
import Note from '../../DocNote.vue'
import ContentHeader from '../../ContentHeader.vue'
import CodeBlock from '../../CodeBlock.vue'
</script>
<template>
    <p>
        Internally, the workshop framework uses a
        <a target="_blank" href="https://en.wikipedia.org/wiki/Dependency_Injection"
            >dependency injection container</a
        >. This allows you to request other services from the application and replace services with
        your own implementations. We use the
        <a target="_blank" href="http://php-di.org">PHP-DI</a> package for dependency injection, a
        <Code>container-interop</Code> compatible dependency injection container.
    </p>
    <p>
        Services are configured in <Code>app/config.php</Code>. You can use all of the features of
        <a target="_blank" href="http://php-di.org">PHP-DI</a> so check the docs there.
    </p>

    <p>
        The file <Code>app/config.php</Code> should return an array of service definitions for the
        container. The key being the name of the service and the value the actual factory.
    </p>

    <ContentHeader id="defining-factories">Defining Factories</ContentHeader>

    <ContentHeader level="h4" id="no-deps">Simple Object with No dependencies</ContentHeader>
    <p>
        <Code>\DI\Object()</Code> is a helper function to create a factory which will simply run
        <Code>new $yourClassName</Code> when asking for the service from the container.
    </p>
    <CodeBlock lang="php">
        <pre>
return [
    ...snip
    Generator::class => \DI\Object(),
];</pre
        >
    </CodeBlock>
    <ContentHeader level="h4" id="with-deps">Object with dependencies</ContentHeader>

    <p>
        PHP-DI provides more powerful features such as being able to use anonymous functions and any
        valid PHP <Code>callables</Code> as a factory. When using a callable your callable will be
        injected with the container itself which you can pull other services from!
    </p>
    <p>
        If you pulled in a third party library for random number generation you might define a
        service like below. We use the battle tested package:
        <Code>ircmaxell/random-lib</Code> as an example.
    </p>
    <p>
        We use an anonymous function and pull the strength parameter from the container and create a
        new random number generator based on that.
    </p>
    <CodeBlock lang="php">
        <pre>
return [
    ...snip
    \RandomLib\Generator::class => \DI\factory(function (ContainerInterface $container) {
        $strength = $container->get('random-number-generator-strength');
        $factory = new \RandomLib\Factory;
        return $factory->getGenerator(new \SecurityLib\Strength($strength));
    }),
];</pre
        >
    </CodeBlock>

    <ContentHeader id="overwriting-services">Overwriting existing services</ContentHeader>

    <p>
        As your workshop configuration is merged into default workshop framework configuration, you
        can override existing services with your own implementation. Maybe you want to override the
        <Code>\Symfony\Component\Filesystem\Filesystem</Code> service with your own version, maybe
        you extended it to add some methods.
    </p>
    <p>
        The below definition would replace the
        <Code>Symfony\Component\Filesystem\Filesystem</Code> service with your own implementation:
        <Code>MyFileSystem</Code> which extends from
        <Code>\Symfony\Component\Filesystem\Filesystem</Code>.
    </p>

    <CodeBlock lang="php">
        <pre>
return [
    ...snip
    Symfony\Component\Filesystem\Filesystem::class => \DI\factory(function (ContainerInterface $c) {
        return new MyFileSystem;
    }),
];</pre
        >
    </CodeBlock>

    <p>
        Now when you ask the container for
        <Code>Symfony\Component\Filesystem\Filesystem</Code> you will receive an instance of
        <Code>MyFileSystem</Code>.
    </p>
    <Note type="warning"
        >Be aware that anything already using the
        <Code>Symfony\Component\Filesystem\Filesystem</Code> service will expect it to be an
        instance of <Code>\Symfony\Component\Filesystem\Filesystem</Code>, so if you replace it with
        an altogether different object, expect things to break!</Note
    >

    <p>
        Check
        <router-link to="/docs/reference/available-services">Available Services</router-link>
        for a list and description of services available to you.
    </p>
</template>
