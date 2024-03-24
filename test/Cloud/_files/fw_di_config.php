<?php

declare(strict_types=1);

use PhpSchool\WebsiteTest\Asset\CliExerciseImpl;

return [
    \PhpSchool\PhpWorkshop\ExerciseDispatcher::class => function () {
        return new \PhpSchool\PhpWorkshop\ExerciseDispatcher(
            new \PhpSchool\PhpWorkshop\ExerciseRunner\RunnerManager(),
            new \PhpSchool\PhpWorkshop\ResultAggregator(),
            new \PhpSchool\PhpWorkshop\Event\EventDispatcher(
                new \PhpSchool\PhpWorkshop\ResultAggregator()
            ),
            new \PhpSchool\PhpWorkshop\Check\CheckRepository()
        );
    },
    'my-service' => function () {
        return new class () {
            public $name = 'my-service';
        };
    },
    'Exercise1' => function () {
        return new CliExerciseImpl('Exercise 1');
    },
    'Exercise2' => function () {
        return new CliExerciseImpl('Exercise 2');
    },
    'Exercise3' => function () {
        return new CliExerciseImpl('Exercise 3');
    },
];
