<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online\Sandbox;

use PhpSchool\PhpWorkshop\Check\CodeExistsCheck;
use PhpSchool\PhpWorkshop\Check\CodeParseCheck;
use PhpSchool\PhpWorkshop\Check\FileExistsCheck;
use PhpSchool\PhpWorkshop\Check\PhpLintCheck;
use PhpSchool\PhpWorkshop\Event\CliExecuteEvent;
use PhpSchool\PhpWorkshop\Event\Event;
use PhpSchool\PhpWorkshop\Event\EventDispatcher;
use PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent;
use PhpSchool\PhpWorkshop\Exception\CodeExecutionException;
use PhpSchool\PhpWorkshop\Exception\SolutionExecutionException;
use PhpSchool\PhpWorkshop\Exercise\CliExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\ExerciseRunner\ExerciseRunnerInterface;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\Output\OutputInterface;
use PhpSchool\PhpWorkshop\Result\Cli\CliResult;
use PhpSchool\PhpWorkshop\Result\Cli\GenericFailure;
use PhpSchool\PhpWorkshop\Result\Cli\RequestFailure;
use PhpSchool\PhpWorkshop\Result\Cli\ResultInterface as CliResultInterface;
use PhpSchool\PhpWorkshop\Result\Cli\Success;
use PhpSchool\PhpWorkshop\Result\ResultInterface;
use PhpSchool\PhpWorkshop\Utils\ArrayObject;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class SandboxedCliRunner implements ExerciseRunnerInterface
{
    public const ROOT_ARGUMENT = 'solution-root';

    private string $phpLocation;

    public function __construct(
        private readonly CliExercise&ExerciseInterface $exercise,
        private readonly EventDispatcher $eventDispatcher,
        private readonly PhpSandbox $sandbox,
    ) {
        $php = (new ExecutableFinder())->find('php');

        if (null === $php) {
            throw new RuntimeException('Could not load php binary. Please install php using your package manager.');
        }

        $this->phpLocation = $php;
    }

    public function getName(): string
    {
        return 'CLI Program Runner';
    }

    /**
     * @return array<class-string>
     */
    public function getRequiredChecks(): array
    {
        return [
            FileExistsCheck::class,
            CodeExistsCheck::class,
            PhpLintCheck::class,
            CodeParseCheck::class,
        ];
    }

    public function verify(Input $input): ResultInterface
    {
        $this->eventDispatcher->dispatch(new ExerciseRunnerEvent('cli.verify.start', $this->exercise, $input));
        $result = new CliResult(
            array_map(
                fn(array $args) => $this->doVerify($args, $input),
                $this->preserveOldArgFormat($this->exercise->getArgs())
            )
        );
        $this->eventDispatcher->dispatch(new ExerciseRunnerEvent('cli.verify.finish', $this->exercise, $input));
        return $result;
    }

    public function run(Input $input, OutputInterface $output): bool
    {
        $this->eventDispatcher->dispatch(new ExerciseRunnerEvent('cli.run.start', $this->exercise, $input));
        $success = true;
        foreach ($this->preserveOldArgFormat($this->exercise->getArgs()) as $args) {
            /** @var CliExecuteEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new CliExecuteEvent('cli.run.student-execute.pre', new ArrayObject($args))
            );

            $args = $event->getArgs();

            $process = $this->getStudentProcess($input, $args);
            $process->start();
            $this->eventDispatcher->dispatch(
                new CliExecuteEvent('cli.run.student.executing', $args, ['output' => $output])
            );
            $process->wait(function ($outputType, $outputBuffer) use ($output) {
                $output->write($outputBuffer);
            });
            $output->emptyLine();

            if (!$process->isSuccessful()) {
                $success = false;
            }

            $output->lineBreak();

            $this->eventDispatcher->dispatch(
                new CliExecuteEvent('cli.run.student-execute.post', $args)
            );
        }

        $this->eventDispatcher->dispatch(new ExerciseRunnerEvent('cli.run.finish', $this->exercise, $input));
        return $success;
    }

    /**
     * @param array<string> $args
     */
    private function doVerify(array $args, Input $input): CliResultInterface
    {
        $args = new ArrayObject($args);

        try {
            /** @var CliExecuteEvent $event */
            $event = $this->eventDispatcher->dispatch(new CliExecuteEvent('cli.verify.reference-execute.pre', $args));
            $solutionOutput = $this->execute(
                $this->getReferenceProcess($event->getArgs()),
                $event->getArgs(),
                'reference'
            );
        } catch (CodeExecutionException $e) {
            $this->eventDispatcher->dispatch(new Event('cli.verify.reference-execute.fail', ['exception' => $e]));
            throw new SolutionExecutionException($e->getMessage());
        }

        try {
            /** @var CliExecuteEvent $event */
            $event = $this->eventDispatcher->dispatch(new CliExecuteEvent('cli.verify.student-execute.pre', $args));
            $userOutput = $this->execute(
                $this->getStudentProcess($input, $event->getArgs()),
                $event->getArgs(),
                'student'
            );
        } catch (CodeExecutionException $e) {
            $this->eventDispatcher->dispatch(new Event('cli.verify.student-execute.fail', ['exception' => $e]));
            return GenericFailure::fromArgsAndCodeExecutionFailure($args, $e);
        }
        if ($solutionOutput === $userOutput) {
            return new Success($event->getArgs());
        }

        return RequestFailure::fromArgsAndOutput($event->getArgs(), $solutionOutput, $userOutput);
    }

    /**
     * @param ArrayObject<int, string> $args
     */
    private function execute(Process $process, ArrayObject $args, string $type): string
    {
        $process->start();
        $this->eventDispatcher->dispatch(new CliExecuteEvent(sprintf('cli.verify.%s.executing', $type), $args));
        $process->wait();

        if (!$process->isSuccessful()) {
            throw CodeExecutionException::fromProcess($process);
        }

        return $process->getOutput();
    }

    /**
     * @param ArrayObject<int, string> $args
     */
    private function getReferenceProcess(ArrayObject $args): Process
    {
        $fileName = $this->exercise->getSolution()->getEntryPoint()->getAbsolutePath();

        return new Process(
            [$this->phpLocation, $fileName, ...$args->getArrayCopy()],
            dirname($fileName),
            $this->sandbox->environment(['XDEBUG_MODE' => 'off']),
            null,
            10
        );
    }

    /**
     * @param ArrayObject<int, string> $args
     */
    public function getStudentProcess(Input $input, ArrayObject $args): Process
    {
        $fileName = $input->getRequiredArgument('program');
        $root = $input->getArgument(self::ROOT_ARGUMENT) ?? dirname($fileName);

        return new Process(
            [
                $this->phpLocation,
                ...$this->sandbox->iniArguments(
                    $root,
                    $this->sandbox->readablePathsFromArguments($args->getArrayCopy())
                ),
                $fileName,
                ...$args->getArrayCopy(),
            ],
            dirname($fileName),
            $this->sandbox->environment(['XDEBUG_MODE' => 'off']),
            null,
            10
        );
    }

    /**
     * @param array<mixed> $args
     * @return array<array<string>>
     */
    private function preserveOldArgFormat(array $args): array
    {
        if (isset($args[0]) && !is_array($args[0])) {
            $args = [$args];
        } elseif (count($args) === 0) {
            $args = [[]];
        }

        /** @var array<array<string>> $args */
        return $args;
    }
}
