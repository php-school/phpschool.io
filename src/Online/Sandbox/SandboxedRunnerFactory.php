<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online\Sandbox;

use PhpSchool\PhpWorkshop\CommandArgument;
use PhpSchool\PhpWorkshop\CommandDefinition;
use PhpSchool\PhpWorkshop\Event\EventDispatcher;
use PhpSchool\PhpWorkshop\Exercise\CgiExercise;
use PhpSchool\PhpWorkshop\Exercise\CliExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Exercise\ExerciseType;
use PhpSchool\PhpWorkshop\ExerciseRunner\ExerciseRunnerInterface;
use PhpSchool\PhpWorkshop\ExerciseRunner\Factory\ExerciseRunnerFactoryInterface;
use RuntimeException;

class SandboxedRunnerFactory implements ExerciseRunnerFactoryInterface
{
    public function __construct(
        private readonly EventDispatcher $eventDispatcher,
        private readonly PhpSandbox $sandbox,
    ) {}

    public function supports(ExerciseInterface $exercise): bool
    {
        return in_array($exercise->getType()->getValue(), [ExerciseType::CLI, ExerciseType::CGI], true);
    }

    public function configureInput(CommandDefinition $commandDefinition): void
    {
        $commandDefinition->addArgument(CommandArgument::required('program'));
    }

    public function create(ExerciseInterface $exercise): ExerciseRunnerInterface
    {
        if ($exercise instanceof CliExercise && $exercise->getType()->getValue() === ExerciseType::CLI) {
            return new SandboxedCliRunner($exercise, $this->eventDispatcher, $this->sandbox);
        }

        if ($exercise instanceof CgiExercise && $exercise->getType()->getValue() === ExerciseType::CGI) {
            return new SandboxedCgiRunner($exercise, $this->eventDispatcher, $this->sandbox);
        }

        throw new RuntimeException(sprintf('Exercise "%s" cannot be run in the sandbox', $exercise->getName()));
    }
}
