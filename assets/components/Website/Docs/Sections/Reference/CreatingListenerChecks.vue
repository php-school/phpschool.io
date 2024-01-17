<script setup>
import Code from '../../DocCode.vue'
import Note from '../../DocNote.vue'
import ContentHeader from '../../ContentHeader.vue'
import DocTerminal from '../../DocTerminal.vue'
import CodeBlock from '../../CodeBlock.vue'
</script>
<template>
    <p>
        In the previous section, we learned of all the events dispatched throughout the process of
        verifying and running a student's solution to an exercise. In this this section we will
        learn how these events can be used to build a
        <em>Listener Check</em>.
    </p>

    <ContentHeader id="what-is-a-listener-check">What is a Listener Check?</ContentHeader>

    <p>
        We learned about <em>Simple Checks</em> in
        <router-link to="/docs/reference/exercise-checks">Exercise Checks</router-link>, they are
        simple pieces of code which can run before or after verifying a student's solution to an
        exercise. <em>Listener Checks</em> allow us to hook in to the verifying and running process
        with more granular precision. <em>Listener Checks</em> can run pieces of code at any point
        where an event is dispatched. Check the
        <router-link to="/docs/reference/events">Events</router-link> page for a list of available
        events which your <em>Listener Check</em> can listen to.
    </p>

    <p>
        Listener Checks are one of the most complex components of the workshop application, so in
        order to demonstrate their use-case, we will build a
        <em>Listener Check</em> which allows us to interact with
        <a href="http://couchdb.apache.org/">Couch DB</a>. We will then build an exercise in our
        tutorial application which utilises this check.
    </p>

    <ul>
        <li>
            <a target="_blank" href="https://github.com/php-school/couch-db-check"
                >The finished Couch DB Check</a
            >
        </li>
        <li>
            <a
                target="_blank"
                href="https://github.com/php-school/simple-math/compare/couch-db-exercise?expand=1"
                >Exercise utilising the check</a
            >
        </li>
    </ul>

    <ContentHeader id="check-specification">Check Specification</ContentHeader>
    <p>Before we build anything we should design our check. What should it do?</p>

    <p>
        Couch DB is a NoSQL database, which stores data as JSON documents and it's API is provided
        via regular HTTP.
    </p>

    <p>
        So, we want to introduce the features of Couch DB via this Listener Check. What should it
        do?
    </p>

    <ul>
        <li>Be applicable to only <Code>CLI</Code> type exercises.</li>
        <li>
            Create 2 databases, one for the student solution and one for the reference solution.
        </li>
        <li>Pass the databases names to the programs.</li>
        <li>
            Remove the databases at the end of the verify/run process and in case of any failures.
        </li>
        <li>Allow for exercises to seed the two databases with data.</li>
        <li>
            Allow for exercises to verify the data in the database after the solutions have
            executed.
        </li>
    </ul>

    <ContentHeader id="check-events">What events to use?</ContentHeader>

    <p>
        Reading this specification we can see that we will need to hook in to various events to
        provide this functionality, we will now break down each point and decide what events to
        listen to.
    </p>

    <ContentHeader level="h4" id="db-create">Creating the databases</ContentHeader>
    <p>
        We will need to create databases in both <Code>verify</Code> & <Code>run</Code> mode, we can
        do this immediately in our <Code>attach</Code> method, which is automatically called when we
        register our check within an exercise.
    </p>

    <ContentHeader level="h4" id="db-seed">Seed the database</ContentHeader>
    <p>
        We will need to allow the exercise to seed the database, we should do this early on
        <Code>verify.start</Code> & <Code>run.start</Code> are the earliest events dispatched. These
        sound like good candidates to perform this task. We will pass a client object to the
        exercise <Code>seed</Code> method so they can create documents.
    </p>

    <ContentHeader level="h4" id="db-arg">Pass database name to the programs</ContentHeader>
    <p>
        We will need to pass the database names to the programs (student's solution & the reference
        solution) so the programs can access it via the
        <Code>$argv</Code> array. We can do this with any events which trigger with an instance of
        <Code>CliExecuteEvent</Code>. We can use <Code>cli.verify.reference-execute.pre</Code>,
        <Code>cli.verify.student-execute.pre</Code> & <Code>cli.run.student-execute.pre</Code>.
    </p>

    <ContentHeader level="h4" id="db-verify">Verify the database</ContentHeader>

    <p>
        We will need to allow the exercise to verify the database, we should do this after output
        verification has finished. We can pick one of the last events triggered,
        <Code>verify.finish</Code> will do! We will pass the database client object again to the
        exercise <Code>verify</Code> method so they can verify the state of the database.
    </p>

    <ContentHeader level="h4" id="db-cleanup">Cleanup the database</ContentHeader>
    <p>
        We will need to remove the databases we created at the end of the process. We can use
        <Code>verify.finish</Code> & <Code>run.finish</Code> to do this. We will also listen to
        <Code>cli.verify.reference-execute.fail</Code> so in case something goes wrong, we still
        cleanup.
    </p>

    <ContentHeader id="build-it">Now let's build the check!</ContentHeader>

    <p>
        The finished
        <a href="https://github.com/php-school/couch-db-check">Couch DB check</a> is available as a
        separate Composer package for you to use in your workshops right away, but, for the sake of
        this tutorial we will build it using the
        <a href="https://github.com/php-school/simple-math">tutorial application</a>
        as a base so we do not have to setup a new project with composer files, register it with
        <a href="https://packagist.org/">Packagist</a> and so on.
    </p>

    <p>
        We will start fresh from the <Code>master</Code> branch for this tutorial, so if you haven't
        already got it, git clone it and install the dependencies:
    </p>

    <DocTerminal
        :lines="[
            'cd projects',
            'git clone git@github.com:php-school/simple-math.git',
            'cd simple-math',
            'composer install'
        ]"
    ></DocTerminal>

    <ContentHeader level="h4" id="check-step-1"
        >1. Require doctrine/couchdb as a dependency</ContentHeader
    >
    <p>We will use this library to interact with Couch DB.</p>
    <DocTerminal
        :lines="['composer require &quot;doctrine/couchdb:^1.0@beta&quot;']"
    ></DocTerminal>

    <ContentHeader level="h4" id="check-step-2">2. Create the folders and classes</ContentHeader>
    <DocTerminal
        :lines="[
            'mkdir src/Check',
            'mkdir src/ExerciseCheck',
            'touch src/Check/CouchDbCheck.php',
            'touch src/ExerciseCheck/CouchDbExerciseCheck.php'
        ]"
    ></DocTerminal>

    <ContentHeader level="h4" id="check-step-3">3. Define our interface</ContentHeader>
    <p>
        We mentioned before that we needed a way for the exercise to seed and verify the database,
        so we will define an interface which describes these methods which the exercise must
        implement for the Couch DB check. These methods will automatically be invoked by the check.
        Open up
        <Code>src/ExerciseCheck/CouchDbExerciseCheck.php</Code> and add the following code to it:
    </p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

namespace PhpSchool\SimpleMath\ExerciseCheck;

use Doctrine\CouchDB\CouchDBClient;

interface CouchDbExerciseCheck
{
    /**
     * @param CouchDBClient $couchDbClient
     * @return void
     */
    public function seed(CouchDBClient $couchDbClient);

    /**
     * @param CouchDBClient $couchDbClient
     * @return bool
     */
    public function verify(CouchDBClient $couchDbClient);
}
</pre
        >
    </CodeBlock>

    <p>
        We define, two methods <Code>seed()</Code> & <Code>verify()</Code>, both receive an instance
        of <code>CouchDBClient</code> which will be connected to the database created for the
        student, <Code>seed()</Code> should be called before the student's solution is run and
        <Code>verify()</Code> should be called after the student's solution is run.
    </p>

    <ContentHeader level="h4" id="check-step-4">4. Write the check</ContentHeader>

    <Note type="info"
        >For this check, we assume that Couch DB is always running at
        <Code>http://localhost:5984/</Code>as is default when Couch DB is installed.</Note
    >

    <p>
        Now we write the check - there is quite a lot of code here so we will do it in steps, open
        up <Code>src/Check/CouchDbCheck.php</Code> and start with the following:
    </p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

namespace PhpSchool\SimpleMath;

use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\HTTP\HTTPException;
use PhpSchool\PhpWorkshop\Check\ListenableCheckInterface;
use PhpSchool\PhpWorkshop\Event\EventDispatcher;
use PhpSchool\SimpleMath\ExerciseCheck\CouchDbExerciseCheck;

class CouchDbCheck implements ListenableCheckInterface
{
    /**
     * @var string
     */
    private static $studentDb = 'phpschool-student';

    /**
     * @var string
     */
    private static $solutionDb = 'phpschool';

    /**
     * Return the check's name
     *
     * @return string
     */
    public function getName()
    {
        return 'Couch DB Verification Check';
    }

    /**
     * This returns the interface the exercise should implement
     * when requiring this check
     *
     * @return string
     */
    public function getExerciseInterface()
    {
        return CouchDbExerciseCheck::class;
    }

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function attach(EventDispatcher $eventDispatcher)
    {

    }
}
</pre
        >
    </CodeBlock>

    <p>
        There is not much going on here - we define <Code>getName()</Code> which is the name of our
        check, and <Code>getExerciseInterface()</Code> which should return the FQCN (Fully Qualified
        Class Name) of the interface we just defined earlier. This is so the workshop framework can
        check the exercise implements it. We also define some properties which describe the names of
        the Couch DB databases we will setup: one for the student and one for the reference
        solution.
    </p>

    <p>
        The most important thing to note in this check is that we implement
        <Code>PhpSchool\PhpWorkshop\Check\ListenableCheckInterface</Code> instead of
        <Code>PhpSchool\PhpWorkshop\Check\SimpleCheckInterface</Code>. They both inherit from
        <Code>PhpSchool\PhpWorkshop\Check\CheckInterface</Code> which introduces
        <Code>getName()</Code> & <Code>getExerciseInterface()</Code>.
        <Code>ListenableCheckInterface</Code> brings in one other additional method:
        <Code>attach()</Code>. This method is called immediately when an exercise requires any
        <em>Listener Check</em> and is passed an instance of
        <Code>PhpSchool\PhpWorkshop\Event\EventDispatcher</Code> allowing the check to listen to any
        events which might be dispatched throughout the verifying/running process.
    </p>

    <p>
        Our check will listen to a number of those events so we will build this method up step by
        step.
    </p>

    <ContentHeader level="h5" id="db-create">Create the databases</ContentHeader>
    <p>
        The first thing we need to do is create the two databases, so we create two Couch DB clients
        and issue the <Code>createDatabase</Code> method:
    </p>

    <CodeBlock lang="php">
        <pre>
$studentClient = CouchDBClient::create(['dbname' => static::$studentDb);
$solutionClient = CouchDBClient::create(['dbname' => static::$solutionDb]);

$studentClient->createDatabase($studentClient->getDatabase());
$solutionClient->createDatabase($solutionClient->getDatabase());
</pre
        >
    </CodeBlock>

    <ContentHeader level="h5" id="db-seed">Seed the databases for verify mode</ContentHeader>
    <p>
        We need to allow the exercise to seed the database to create documents, for example. The
        database for the student and the reference solution should contain the same data, but they
        must be different databases.
    </p>

    <Note type="info"
        >The reason why both programs need their own database is fairly simple. Say the exercise's
        lesson was to teach how to remove a document in the database. It would first need to create
        a document in the database using the <Code>seed</Code> method. The student's solution should
        remove that document. If the student's solution and the reference solution shared one
        database, then the reference solution would run first and remove the row. Then the student's
        solution would run...it can't remove the document because it's not there anymore!</Note
    >

    <Note type="danger"
        >We can't just call <Code>seed()</Code> again because <Code>seed()</Code> can return dynamic
        data and then the student's solution and the reference solution would run with different
        data sets; which makes it impossible to compare their output.</Note
    >

    <CodeBlock lang="php">
        <pre>
$eventDispatcher->listen('verify.start', function (Event $e) use ($studentClient, $solutionClient) {
    $e->getParameter('exercise')->seed($studentClient);
    $this->replicateDbFromStudentToSolution($studentClient, $solutionClient);
});
</pre
        >
    </CodeBlock>

    <p>
        We listen to the <Code>verify.start</Code> event which (as you can probably infer) triggers
        right at the start of the verify process. The listener is an anonymous function that grabs
        the exercise instance from the event and calls the <Code>seed()</Code> method passing in the
        <Code>CouchDBClient</Code> which references the database created for the student. We also
        need to seed the database for reference solution, we need it to be exactly the same as the
        student's so we basically select all documents from the student database and insert them in
        to the reference solution database. We do this in the method
        <Code>replicateDbFromStudentToSolution</Code>. This method looks like the following:
    </p>

    <CodeBlock lang="php">
        <pre>
/**
 * @param CouchDBClient $studentClient
 * @param CouchDBClient $solutionClient
 * @throws \Doctrine\CouchDB\HTTP\HTTPException
 */
private function replicateDbFromStudentToSolution(CouchDBClient $studentClient, CouchDBClient $solutionClient)
{
    $response = $studentClient->allDocs();

    if ($response->status !== 200) {
        return;
    }

    foreach ($response->body['rows'] as $row) {
        $doc = $row['doc'];

        $data = array_filter($doc, function ($key) {
            return !in_array($key, ['_id', '_rev']);
        }, ARRAY_FILTER_USE_KEY);

        try {
            $solutionClient->putDocument(
                $data,
                $doc['_id']
            );
        } catch (HTTPException $e) {
        }
    }
}
</pre
        >
    </CodeBlock>

    <ContentHeader level="h5" id="db-seed-run">Seed the database for run mode</ContentHeader>
    <p>
        When in run mode, no output is compared - we merely run the student's solution - so we only
        need to seed the student's database. There is a similar event to
        <Code>verify.start</Code> when in run mode, aptly named <Code>run.start</Code>, let's use
        that:
    </p>

    <CodeBlock lang="php">
        <pre>
$eventDispatcher->listen('run.start', function (Event $e) use ($studentClient) {
    $e->getParameter('exercise')->seed($studentClient);
});
</pre
        >
    </CodeBlock>

    <ContentHeader level="h5" id="db-arg"
        >Adding the database name to the programs' arguments</ContentHeader
    >
    <p>
        We need the programs (student solution & the reference solution) to have access to their
        respective database names, the best way to do this is via command line arguments - we can
        add arguments to the list of arguments to be sent to the programs with any event which
        triggers with an instance of
        <Code>CliExecuteEvent</Code>. It exposes the <Code>prependArg()</Code> &
        <Code>appendArg()</Code> methods.
    </p>

    <p>
        We use <Code>cli.verify.reference-execute.pre</Code> to prepend the reference database name
        to the reference solution program when in <Code>verify</Code> mode and we use
        <Code>cli.verify.student-execute.pre</Code> & <Code>cli.run.student-execute.pre</Code> to
        prepend the student database name to the student solution in <Code>verify</Code> &
        <Code>run</Code> mode, respectively.
    </p>

    <CodeBlock lang="php">
        <pre>
$eventDispatcher->listen('cli.verify.reference-execute.pre', function (CliExecuteEvent $e) {
    $e->prependArg('phpschool');
});

$eventDispatcher->listen(
    ['cli.verify.student-execute.pre', 'cli.run.student-execute.pre'],
    function (CliExecuteEvent $e) {
        $e->prependArg('phpschool-student');
    }
);
</pre
        >
    </CodeBlock>

    <ContentHeader level="h5" id="db-verify">Verify the database</ContentHeader>
    <p>
        After the programs have been executed, we need a way to let the exercise verify the contents
        of the database. We hook on to an event during the
        <Code>verify</Code> process named <Code>verify.finish</Code> (this is the last event in the
        verify process) and insert a verifier function. We don't need to verify the database in
        <Code>run</Code> mode because all we do in run mode is <em>run</em> the students submission
        in the correct environment (with args and database).
    </p>

    <CodeBlock lang="php">
        <pre>
$eventDispatcher->insertVerifier('verify.finish', function (Event $e) use ($studentClient) {
    $verifyResult = $e->getParameter('exercise')->verify($studentClient);

    if (false === $verifyResult) {
        return Failure::fromNameAndReason($this->getName(), 'Database verification failed');
    }

    return Success::fromCheck($this);
});
</pre
        >
    </CodeBlock>

    <p>
        Verify functions are used to inject results into the result set, which is then reported to
        the student. So you can see that if the
        <Code>verify</Code> method returns <Code>true</Code> we return a <Code>Success</Code> to the
        result set but if it returns false we return a <Code>Failure</Code> result, with a message,
        so the student knows what went wrong.
    </p>

    <Note type="success"
        >The Event Dispatcher takes care of running the verifier function at the correct event and
        injects the returned result in to the result set.</Note
    >

    <ContentHeader level="h5" id="db-cleanup">Cleanup the databases</ContentHeader>
    <p>
        The final stage is to remove the databases, we listen to
        <Code>verify.post.execute</Code> for the verify process & <Code>run.finish</Code> for the
        run process:
    </p>

    <CodeBlock lang="php">
        <pre>
$eventDispatcher->listen(
    [
        'verify.post.execute',
        'run.finish'
    ],
    function (Event $e) use ($studentClient, $solutionClient) {
        $studentClient->deleteDatabase(static::$studentDb);
        $solutionClient->deleteDatabase(static::$solutionDb);
    }
);
</pre
        >
    </CodeBlock>

    <Note type="success"
        >Great - our check is finished! You can see the final result as a separate Composer package,
        <a href="https://github.com/php-school/couch-db-check">available here.</a>
    </Note>

    <p>Our final check should look like:</p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

namespace PhpSchool\SimpleMath;

use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\HTTP\HTTPException;
use PhpSchool\PhpWorkshop\Check\ListenableCheckInterface;
use PhpSchool\PhpWorkshop\Event\EventDispatcher;
use PhpSchool\SimpleMath\ExerciseCheck\CouchDbExerciseCheck;

class CouchDbCheck implements ListenableCheckInterface
{
    /**
     * @var string
     */
    private static $studentDb = 'phpschool-student';

    /**
     * @var string
     */
    private static $solutionDb = 'phpschool';

    /**
     * Return the check's name
     *
     * @return string
     */
    public function getName()
    {
        return 'Couch DB Verification Check';
    }

    /**
     * This returns the interface the exercise should implement
     * when requiring this check
     *
     * @return string
     */
    public function getExerciseInterface()
    {
        return CouchDbExerciseCheck::class;
    }

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function attach(EventDispatcher $eventDispatcher)
    {
        $studentClient = CouchDBClient::create(['dbname' => static::$studentDb);
        $solutionClient = CouchDBClient::create(['dbname' => static::$solutionDb]);

        $studentClient->createDatabase($studentClient->getDatabase());
        $solutionClient->createDatabase($solutionClient->getDatabase());

        $eventDispatcher->listen('verify.start', function (Event $e) use ($studentClient, $solutionClient) {
            $e->getParameter('exercise')->seed($studentClient);
            $this->replicateDbFromStudentToSolution($studentClient, $solutionClient);
        });

        $eventDispatcher->listen('run.start', function (Event $e) use ($studentClient) {
            $e->getParameter('exercise')->seed($studentClient);
        });

        $eventDispatcher->listen('cli.verify.reference-execute.pre', function (CliExecuteEvent $e) {
            $e->prependArg('phpschool');
        });

        $eventDispatcher->listen(
            ['cli.verify.student-execute.pre', 'cli.run.student-execute.pre'],
            function (CliExecuteEvent $e) {
                $e->prependArg('phpschool-student');
            }
        );

        $eventDispatcher->listen(
            [
                'verify.post.execute',
                'run.finish'
            ],
            function (Event $e) use ($studentClient, $solutionClient) {
                $studentClient->deleteDatabase(static::$studentDb);
                $solutionClient->deleteDatabase(static::$solutionDb);
            }
        );
    }

    /**
     * @param CouchDBClient $studentClient
     * @param CouchDBClient $solutionClient
     * @throws \Doctrine\CouchDB\HTTP\HTTPException
     */
    private function replicateDbFromStudentToSolution(CouchDBClient $studentClient, CouchDBClient $solutionClient)
    {
        $response = $studentClient->allDocs();

        if ($response->status !== 200) {
            return;
        }

        foreach ($response->body['rows'] as $row) {
            $doc = $row['doc'];

            $data = array_filter($doc, function ($key) {
                return !in_array($key, ['_id', '_rev']);
            }, ARRAY_FILTER_USE_KEY);

            try {
                $solutionClient->putDocument(
                    $data,
                    $doc['_id']
                );
            } catch (HTTPException $e) {
            }
        }
    }
}
</pre
        >
    </CodeBlock>

    <ContentHeader id="using-the-check">Build an exercise using the Couch DB check</ContentHeader>

    <p>
        So then, this Couch DB check is not much use if we don't utilise it! let's build an exercise
        which retrieves a document from a database, sums a bunch of numbers and adds the total to
        the document, finally we should output the total. The document with the numbers in it will
        be automatically created by our exercise in the <Code>seed()</Code> method and will be
        random.
    </p>

    <p>
        As always we will start from a fresh copy of the
        <a href="https://github.com/php-school/simple-math">tutorial application</a>:
    </p>

    <DocTerminal
        :lines="[
            'cd projects',
            'git clone git@github.com:php-school/simple-math.git',
            'cd simple-math',
            'composer install'
        ]"
    ></DocTerminal>

    <p>
        We will use the check that is available in the already built Composer package, so, pull it
        in to your project:
    </p>

    <DocTerminal
        :lines="[
            'composer require &quot;doctrine/couchdb:^1.0@beta&quot;',
            'composer require php-school/couch-db-check'
        ]"
    ></DocTerminal>

    <Note type="info"
        >We have to manually require <Code>doctrine/couchdb</Code> even though it is a dependency of
        <Code>php-school/couch-db-check</Code> because there is no stable release available.
        Indirect dependencies cannot install non-stable versions.</Note
    >

    <ContentHeader id="exercise-problem">Problem file</ContentHeader>

    <p>
        Create a problem file in
        <Code>exercises/couch-db-exercise/problem/problem.md</Code>. Here we describe the problem we
        mentioned earlier when we decided what we wanted our exercise to do:
    </p>

    <CodeBlock lang="md">
        <pre>
Write a program that accepts the name of database and a Couch DB document ID. You should load this document using the
provided ID from the provided database. In the document will be a key named `numbers`. You should add them all up
and add the total to the document under the key `total`. You should save the document and finally output the total to
the console.

You must have Couch DB installed before you run this exercise, you can get it here:
  [http://couchdb.apache.org/#download]()

----------------------------------------------------------------------
## HINTS

You could use a third party library to communicate with the Couch DB instance, see this doctrine library:
  [https://github.com/doctrine/couchdb-client]()

Or you could interact with it using a HTTP client such as Guzzle:
  [https://github.com/guzzle/guzzle]()

Or you could simply use `curl`.

Check out how to interact with Couch DB documents here:
  [http://docs.couchdb.org/en/1.6.1/intro/api.html#documents]()

You will need to do this via PHP.

You specifically need the `GET` and `PUT` methods, or if you are using a library abstraction, you will need to
`find` and `update` the document.


You can use the doctrine library like so:

```php
&lt;?php
require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\CouchDB\CouchDBClient;
$client = CouchDBClient::create(['dbname' => $dbName]);

//get doc
$doc = $client->findDocument($docId);

//update doc
$client->putDocument($updatedDoc, $docId, $docRevision);
```

`{appname}` will be supplying arguments to your program when you run `{appname} verify program.php` so you don't need to supply them yourself. To test your program without verifying it, you can invoke it with `{appname} run program.php`. When you use `run`, you are invoking the test environment that `{appname}` sets up for each exercise.

----------------------------------------------------------------------
</pre
        >
    </CodeBlock>

    <p>
        We note that the student must have Couch DB installed, we give a few links, an example of
        how to use the Doctrine Couch DB client and we describe the actual task.
    </p>

    <ContentHeader id="exercise">Write the exercise</ContentHeader>

    <p>Create the exercise in <Code>src/Exercise/CouchDbExercise.php</Code>:</p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

namespace PhpSchool\SimpleMath\Exercise;

use Doctrine\CouchDB\CouchDBClient;
use PhpSchool\CouchDb\CouchDbCheck;
use PhpSchool\CouchDb\CouchDbExerciseCheck;
use PhpSchool\PhpWorkshop\Exercise\AbstractExercise;
use PhpSchool\PhpWorkshop\Exercise\CliExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;
use PhpSchool\PhpWorkshop\ExerciseDispatcher;

class CouchDbExercise extends AbstractExercise implements
    ExerciseInterface,
    CliExercise,
    CouchDbExerciseCheck
{
    /**
     * @var string
     */
    private $docId;

    /**
     * @var int
     */
    private $total;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Couch DB Exercise';
    }

     /**
     * @return string
     */
    public function getDescription()
    {
        return 'Intro to Couch DB';
    }

    /**
     * @return string[]
     */
    public function getArgs()
    {
        return [$this->docId];
    }

    /**
     * @return ExerciseType
     */
    public function getType()
    {
        return ExerciseType::CLI();
    }

    /**
     * @param ExerciseDispatcher $dispatcher
     */
    public function configure(ExerciseDispatcher $dispatcher)
    {
        $dispatcher->requireCheck(CouchDbCheck::class);
    }

    /**
     * @param CouchDBClient $couchDbClient
     * @return void
     */
    public function seed(CouchDBClient $couchDbClient)
    {
        $numArgs = rand(4, 20);
        $args = [];
        for ($i = 0; $i &lt; $numArgs; $i ++) {
            $args[] = rand(1, 100);
        }

        list($id) = $couchDbClient->postDocument(['numbers' => $args]);

        $this->docId = $id;
        $this->total = array_sum($args);
    }

    /**
     * @param CouchDBClient $couchDbClient
     * @return bool
     */
    public function verify(CouchDBClient $couchDbClient)
    {
        $total = $couchDbClient->findDocument($this->docId);

        return isset($total->body['total']) && $total->body['total'] == $this->total;
    }
}
</pre
        >
    </CodeBlock>

    <p>
        So - in <Code>seed</Code> we create a random number of random numbers and insert a document
        containing these numbers under a key named <Code>numbers</Code>. We store the total (for
        verification purposes) and also the document ID (this is auto generated by Couch DB) so we
        can pass it to the solutions as an argument.
    </p>

    <p>
        In the <Code>verify</Code> method, we try load the document with the stored ID, check for
        the presence of the <Code>total</Code> property and check that it is equal to the stored
        total we set during <Code>seed</Code>.
    </p>

    <p>
        In <Code>configure()</Code> we require our Couch DB check and in <Code>getType()</Code> we
        inform the the workshop framework that this is a CLI type exercise.
    </p>

    <p>In <Code>getArgs()</Code> we return the Document ID we set during <Code>seed</Code>.</p>
    <Note type="info"
        >Because <Code>seed</Code> is invoked from an event which is dispatched before
        <Code>getArgs</Code>, we can rely on anything set there.</Note
    >

    <Note type="success"
        >The students solution would therefore be invoked like:
        <Code>php my-solution.php phpschool-student 18</Code>. The argument
        <Code>phpschool-student</Code> being the database name created for the student by the check
        (remember the check prepends this argument to the argument list) and 18 being the ID of the
        document we created!</Note
    >

    <ContentHeader id="reference-solution">Write the reference solution</ContentHeader>

    <p>
        Our reference solution will also use the Doctrine Couch DB library - let's go ahead and
        create the solution in
        <Code>exercises/couch-db-exercise/solution</Code>. We will need three files
        <Code>composer.json</Code>, <Code>composer.lock</Code> and
        <Code>solution.php:</Code>
    </p>

    <ContentHeader level="h4-code" id="solution-php">solution.php</ContentHeader>
    <CodeBlock lang="php">
        <pre>
&lt;?php
require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\CouchDB\CouchDBClient;

$client = CouchDBClient::create(['dbname' => $argv[1]]);
$doc = $client->findDocument($argv[2])->body;

$total = array_sum($doc['numbers']);
$doc['total'] = $total;
$client->putDocument(['total' => $total, 'numbers' => $doc['numbers']], $argv[2], $doc['_rev']);
echo $total;
</pre
        >
    </CodeBlock>

    <ContentHeader level="h4-code" id="composer-json">composer.json</ContentHeader>
    <CodeBlock lang="json">
        <pre>
{
    "name": "php-school/couch-db-exercise-ref-solution",
    "description": "Intro to Couch DB",
    "require": {
        "doctrine/couchdb": "^1.0@beta"
    }
}
</pre
        >
    </CodeBlock>

    <ContentHeader level="h4-code" id="composer-lock">composer.lock</ContentHeader>
    <Note type="info"
        ><Code>composer.lock</Code> is auto generated by Composer, by running
        <Code>composer install</Code> in <Code>exercises/couch-db-exercise/solution</Code></Note
    >

    <ContentHeader id="wire-it-together">Wire it all together</ContentHeader>

    <p>
        Now we have to add the factories for our check and exercise and register it with the
        application, add the following to <Code>app/config.php</Code> and don't forget to import the
        necessary classes.
    </p>

    <CodeBlock lang="php">
        <pre>
CouchDbExercise::class => object(),
CouchDbCheck::class => object(),
</pre
        >
    </CodeBlock>

    <p>The result should look like:</p>

    <CodeBlock lang="php">
        <pre>
&lt;?php

use function DI\factory;
use function DI\object;
use Interop\Container\ContainerInterface;
use PhpSchool\SimpleMath\Exercise\GetExercise;
use PhpSchool\CouchDb\CouchDbCheck;
use PhpSchool\SimpleMath\Exercise\CouchDbExercise;
use PhpSchool\SimpleMath\Exercise\Mean;
use PhpSchool\SimpleMath\Exercise\PostExercise;
use PhpSchool\SimpleMath\MyFileSystem;

return [
    //Define your exercise factories here
    Mean::class => factory(function (ContainerInterface $c) {
        return new Mean($c->get(\Symfony\Component\Filesystem\Filesystem::class));
    }),

    CouchDbExercise::class => object(),
    CouchDbCheck::class => object(),
];
</pre
        >
    </CodeBlock>

    <p>
        Finally we need to tell the application about our new check and exercise in
        <Code>app/bootstrap.php</Code>. After the application object is created you just call
        <Code>addCheck</Code> & <Code>addExercise</Code> with the name of check class and exercise
        class respectively. Your final <Code>app/bootstrap.php</Code> file should look something
        like:
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

use PhpSchool\CouchDb\CouchDbCheck;
use PhpSchool\PhpWorkshop\Application;
use PhpSchool\SimpleMath\Exercise\CouchDbExercise;
use PhpSchool\SimpleMath\Exercise\Mean;

$app = new Application('Simple Math', __DIR__ . '/config.php');

$app->addExercise(Mean::class);
$app->addExercise(CouchDbExercise::class);
$app->addCheck(CouchDbCheck::class);

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

    <Note type="success">Our exercise is complete - let's try it out!</Note>

    <ContentHeader id="try-it-out">Try it out!</ContentHeader>

    <p>
        Make sure you have Couch DB installed, run the workshop and select the
        <Code>Couch DB Exercise</Code> exercise.
    </p>

    <p>
        Try verifying with the solution below which incorrectly sets the total to
        <Code>30</Code>, hopefully you will see a failure.
    </p>

    <CodeBlock lang="php">
        <pre>
&lt;?php
require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\CouchDB\CouchDBClient;

$client = CouchDBClient::create(['dbname' => $argv[1]]);
$doc = $client->findDocument($argv[2])->body;

$total = 30; //we guess total is 30
$doc['total'] = $total;
$client->putDocument(['total' => $total, 'numbers' => $doc['numbers']], $argv[2], $doc['_rev']);
echo $total;
</pre
        >
    </CodeBlock>

    <a href="/img/couch-db-fail.png">
        <img src="../../../../../img/cloud/docs/couch-db-fail.png" class="doc-terminal-screen" />
    </a>

    <p>And a solution which does pass will yield the output:</p>

    <a href="/img/couch-db-success.png">
        <img src="../../../../../img/cloud/docs/couch-db-success.png" class="doc-terminal-screen" />
    </a>
</template>
