<script setup>
import Code from '../../DocCode.vue'
import Note from '../../DocNote.vue'
import ContentHeader from '../../ContentHeader.vue'
import CodeBlock from '../../CodeBlock.vue'
</script>
<template>
    <p>
        This is the fun bit! - In this article we will show how we can modify the student's
        solution, injecting, modifying and wrapping code. Before we get down to it, a little
        background to explain why we built this feature.
    </p>

    <h3 id="why">Why?</h3>
    <p>
        We wanted a way to make sure that <Code>display_errors</Code> and
        <Code>error_reporting</Code> were always configured correctly, we also thought that we might
        want to wrap solutions in <Code>try/catch</Code> blocks so we could give more structured
        feedback to the student. We figured, in order to do this in a robust manner, we would have
        to patch the student's solution on the fly and revert the changes after the framework has
        verified/run the solution.
    </p>

    <p>
        We decided this feature may be useful for workshop developers, we thought there may be
        possibilities where you want to concentrate on a verify specific problem like "Here is a
        variable - transform it to this", well with this feature, you could inject that variable at
        the start of the script so it is already available to the student!
    </p>

    <h3 id="how">How?</h3>
    <p>There are two type of modifications you can do to a solution:</p>
    <ul>
        <li>Insertion (Insert code at the beginning or end of a solution)</li>
        <li>Transformer (Use a callable to modify an AST representation of the solution)</li>
    </ul>

    <p>
        In order to inform the workshop framework that an exercise would like to patch a solution it
        must implement
        <Code>PhpSchool\PhpWorkshop\Exercise\SubmissionPatchable</Code> and return an instance of
        <Code>PhpSchool\PhpWorkshop\Patch</Code>.
    </p>

    <p>
        The <Code>Patch</Code> object is where you specify your <em>Insertions</em> &
        <em>Transformers</em>. The API looks like this:
    </p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

use PhpSchool\PhpWorkshop\Patch;

$patch = new Patch;
$patch = $patch->withInsertion($insertion1);
$patch = $patch->withInsertion($insertion2);
$patch = $patch->withTransformer($transformer);
</pre
        >
    </CodeBlock>

    <Note type="info"
        >The <Code>Patch</Code> class is immutable so you will need to assign the result of any
        calls to <Code>with*</Code> methods.</Note
    >

    <ContentHeader id="insertions">Insertions</ContentHeader>
    <p>
        Insertions allow to insert a block of code at either the beginning or end of the student's
        solution. The API is very simple:
    </p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

use PhpSchool\PhpWorkshop\CodeInsertion;

$before = new CodeInsertion(CodeInsertion::TYPE_BEFORE, 'echo "Before";');
$after = new CodeInsertion(CodeInsertion::TYPE_AFTER, 'echo "After";');
</pre
        >
    </CodeBlock>

    <ContentHeader id="transformers">Transformers</ContentHeader>
    <p>
        Transformers allow to modify the whole solution via an AST. A transformer is any valid PHP
        <Code>callable</Code> that returns an <Code>array</Code> of
        <Code>PhpParser\Node</Code> objects. The callable will be passed an <Code>array</Code> of
        <Code>PhpParser\Node</Code> objects which represent the parsed student's solution.
    </p>

    <p>
        Lets see how you can build a transformer that wraps the solution in a
        <Code>try/catch</Code> block that then outputs the exception message.
    </p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

use PhpSchool\PhpWorkshop\Exercise\SubmissionPatchable;
use PhpSchool\PhpWorkshop\Patch;
use PhpSchool\PhpWorkshop\CodeInsertion;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\TryCatch;

class MyExercise extends AbstractExercise implements
    ExerciseInterface,
    SubmissionPatchable
{
    ...snip

    /**
     * @return Patch
     */
    public function getPatch()
    {
        $wrapInTryCatch = function (array $statements) {
                return [
                   new TryCatch(
                       $statements,
                       [
                           new Catch_(
                               new Name('Exception'),
                               'e',
                               [
                                   new Echo_([
                                       new MethodCall(new Variable('e'), 'getMessage')
                                   ])
                               ]
                           )
                       ]
                   )
                ];
            };

       return (new Patch)
            ->withTransformer($wrapInTryCatch);
    }
}
</pre
        >
    </CodeBlock>

    <p>
        Note that the AST modification is fairly complicated, the feature is provided by the
        <a href="https://github.com/nikic/PHP-Parser">nikic/php-parser</a> library and you should
        refer to that project for documentation on the AST.
    </p>

    <ContentHeader id="wiring-it-together">Wiring it together</ContentHeader>
    <p>
        Let's write a patch that will wrap the solution in a
        <Code>try/catch</Code> block, add <Code>echo 'Start';</Code> at the beginning and add
        <Code>echo 'Finish';</Code> at the end:
    </p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\TryCatch;
use PhpSchool\PhpWorkshop\CodeInsertion;
use PhpSchool\PhpWorkshop\Exercise\AbstractExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\SubmissionPatchable;
use PhpSchool\PhpWorkshop\Patch;

class MyExercise extends AbstractExercise implements
    ExerciseInterface,
    SubmissionPatchable
{
    ...snip

    /**
     * @return Patch
     */
    public function getPatch()
    {
        $wrapInTryCatch = function (array $statements) {
                return [
                   new TryCatch(
                       $statements,
                       [
                           new Catch_(
                               new Name('Exception'),
                               'e',
                               [
                                   new Echo_([
                                       new MethodCall(new Variable('e'), 'getMessage')
                                   ])
                               ]
                           )
                       ]
                   )
                ];
            };

       return (new Patch)
           ->withTransformer($wrapInTryCatch)
           ->withInsertion(new CodeInsertion(CodeInsertion::TYPE_BEFORE, "echo 'Start';"))
           ->withInsertion(new CodeInsertion(CodeInsertion::TYPE_AFTER, "echo 'Finish';"));
    }
}
</pre
        >
    </CodeBlock>

    <p>If the following solution was submitted:</p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

echo "Hello World"
throw new InvalidArgumentException('What is this magic?');
</pre
        >
    </CodeBlock>

    <p>Then the code that is actually invoked by the workshop framework would be the following:</p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

echo 'Start';
try {
    echo "Hello World"
    throw new InvalidArgumentException('What is this magic?');
} catch (Exception $e) {
    echo $e->getMessage();
}
echo 'Finish';
</pre
        >
    </CodeBlock>

    <Note type="info"
        >The students solution will be reverted to the original form at the end of the
        verifying/running process so the student will never see the code in their solution
        file.</Note
    >
</template>
