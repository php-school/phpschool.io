<?php

namespace PhpSchool\WebsiteTest\Cloud;

use PhpSchool\PhpWorkshop\Application;
use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\Website\Cloud\CloudInstalledWorkshop;
use PhpSchool\Website\Entity\Workshop;
use PHPUnit\Framework\TestCase;

class CloudInstalledWorkshopTest extends TestCase
{
    public function testWorkshopGetters(): void
    {
        $workshop = new Workshop(
            'php-school',
            'my-workshop',
            'myworkshop',
            'My Workshop',
            'My Workshop',
            'aydin@hotmail.co.uk',
            'Aydin Hassan'
        );

        $app = new Application('My Workshop', __DIR__ . '/_files/fw_di_config.php');

        $cloudWorkshop = new CloudInstalledWorkshop($app, $workshop);

        $this->assertEquals('myworkshop', $cloudWorkshop->getCode());
        $this->assertEquals('My Workshop', $cloudWorkshop->getName());
        $this->assertEquals('My Workshop', $cloudWorkshop->getDescription());
        $this->assertEquals('community', $cloudWorkshop->getType());
    }

    public function testGetExerciseDispatcher(): void
    {
        $workshop = new Workshop(
            'php-school',
            'my-workshop',
            'myworkshop',
            'My Workshop',
            'My Workshop',
            'aydin@hotmail.co.uk',
            'Aydin Hassan'
        );

        $app = new Application('My Workshop', __DIR__ . '/_files/fw_di_config.php');

        $cloudWorkshop = new CloudInstalledWorkshop($app, $workshop);

        $this->assertInstanceOf(ExerciseDispatcher::class, $cloudWorkshop->getExerciseDispatcher());
    }

    public function testGetService(): void
    {
        $workshop = new Workshop(
            'php-school',
            'my-workshop',
            'myworkshop',
            'My Workshop',
            'My Workshop',
            'aydin@hotmail.co.uk',
            'Aydin Hassan'
        );

        $app = new Application('My Workshop', __DIR__ . '/_files/fw_di_config.php');

        $cloudWorkshop = new CloudInstalledWorkshop($app, $workshop);

        $this->assertEquals('my-service', $cloudWorkshop->getService('my-service')->name);
    }

    public function testFindAllExercises(): void
    {
        $workshop = new Workshop(
            'php-school',
            'my-workshop',
            'myworkshop',
            'My Workshop',
            'My Workshop',
            'aydin@hotmail.co.uk',
            'Aydin Hassan'
        );

        $app = new Application('My Workshop', __DIR__ . '/_files/fw_di_config.php');
        $app->addExercise('Exercise1');
        $app->addExercise('Exercise2');
        $app->addExercise('Exercise3');

        $cloudWorkshop = new CloudInstalledWorkshop($app, $workshop);

        $exercises = $cloudWorkshop->findAllExercises();

        $this->assertCount(3, $exercises);
    }

    public function testFindExerciseByNameThrowsExceptionWhenExerciseWithNameDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot find workshop exercise with name: "Exercise 5"');

        $workshop = new Workshop(
            'php-school',
            'my-workshop',
            'myworkshop',
            'My Workshop',
            'My Workshop',
            'aydin@hotmail.co.uk',
            'Aydin Hassan'
        );

        $app = new Application('My Workshop', __DIR__ . '/_files/fw_di_config.php');
        $app->addExercise('Exercise1');
        $app->addExercise('Exercise2');
        $app->addExercise('Exercise3');

        $cloudWorkshop = new CloudInstalledWorkshop($app, $workshop);
        $cloudWorkshop->findExerciseByName('Exercise 5');
    }

    public function testFindExerciseByName(): void
    {
        $workshop = new Workshop(
            'php-school',
            'my-workshop',
            'myworkshop',
            'My Workshop',
            'My Workshop',
            'aydin@hotmail.co.uk',
            'Aydin Hassan'
        );

        $app = new Application('My Workshop', __DIR__ . '/_files/fw_di_config.php');
        $app->addExercise('Exercise1');
        $app->addExercise('Exercise2');
        $app->addExercise('Exercise3');

        $cloudWorkshop = new CloudInstalledWorkshop($app, $workshop);

        $exercise = $cloudWorkshop->findExerciseByName('Exercise 2');

        $this->assertEquals('Exercise 2', $exercise->getName());
    }

    public function testFindNextExerciseReturnsNullWhenNoNextExercise(): void
    {
        $workshop = new Workshop(
            'php-school',
            'my-workshop',
            'myworkshop',
            'My Workshop',
            'My Workshop',
            'aydin@hotmail.co.uk',
            'Aydin Hassan'
        );

        $app = new Application('My Workshop', __DIR__ . '/_files/fw_di_config.php');
        $app->addExercise('Exercise1');
        $app->addExercise('Exercise2');
        $app->addExercise('Exercise3');

        $cloudWorkshop = new CloudInstalledWorkshop($app, $workshop);

        $exercise = $cloudWorkshop->findExerciseByName('Exercise 3');
        $this->assertNull($cloudWorkshop->findNextExercise($exercise));
    }

    public function testFindNextExerciseReturnsNextExercise(): void
    {
        $workshop = new Workshop(
            'php-school',
            'my-workshop',
            'myworkshop',
            'My Workshop',
            'My Workshop',
            'aydin@hotmail.co.uk',
            'Aydin Hassan'
        );

        $app = new Application('My Workshop', __DIR__ . '/_files/fw_di_config.php');
        $app->addExercise('Exercise1');
        $app->addExercise('Exercise2');
        $app->addExercise('Exercise3');

        $cloudWorkshop = new CloudInstalledWorkshop($app, $workshop);

        $exercise = $cloudWorkshop->findExerciseByName('Exercise 2');
        $next = $cloudWorkshop->findNextExercise($exercise);

        $this->assertEquals('Exercise 3', $next->getName());
    }

    public function testJsonSerialise(): void
    {
        $workshop = new Workshop(
            'php-school',
            'my-workshop',
            'myworkshop',
            'My Workshop',
            'My Workshop Description',
            'aydin@hotmail.co.uk',
            'Aydin Hassan'
        );

        $app = new Application('My Workshop', __DIR__ . '/_files/fw_di_config.php');
        $app->addExercise('Exercise1');
        $app->addExercise('Exercise2');
        $app->addExercise('Exercise3');

        $this->assertEquals(
            [
                'name' => 'My Workshop',
                'code' => 'myworkshop',
                'description' => 'My Workshop Description',
                'type' => 'community',
                'exercises' => [
                    [
                        'name' => 'Exercise 1',
                        'description' => 'Exercise 1',
                        'type' => 'CLI'
                    ],
                    [
                        'name' => 'Exercise 2',
                        'description' => 'Exercise 2',
                        'type' => 'CLI'
                    ],
                    [
                        'name' => 'Exercise 3',
                        'description' => 'Exercise 3',
                        'type' => 'CLI'
                    ]
                ]
            ],
            (new CloudInstalledWorkshop($app, $workshop))->jsonSerialize()
        );
    }
}
