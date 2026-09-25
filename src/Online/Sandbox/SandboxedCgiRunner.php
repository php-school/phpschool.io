<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online\Sandbox;

use GuzzleHttp\Psr7\Message;
use PhpSchool\PhpWorkshop\Check\CodeExistsCheck;
use PhpSchool\PhpWorkshop\Check\CodeParseCheck;
use PhpSchool\PhpWorkshop\Check\FileExistsCheck;
use PhpSchool\PhpWorkshop\Check\PhpLintCheck;
use PhpSchool\PhpWorkshop\Event\CgiExecuteEvent;
use PhpSchool\PhpWorkshop\Event\Event;
use PhpSchool\PhpWorkshop\Event\EventDispatcher;
use PhpSchool\PhpWorkshop\Event\ExerciseRunnerEvent;
use PhpSchool\PhpWorkshop\Exception\CodeExecutionException;
use PhpSchool\PhpWorkshop\Exception\SolutionExecutionException;
use PhpSchool\PhpWorkshop\Exercise\CgiExercise;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\ExerciseRunner\ExerciseRunnerInterface;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\Output\OutputInterface;
use PhpSchool\PhpWorkshop\Result\Cgi\CgiResult;
use PhpSchool\PhpWorkshop\Result\Cgi\GenericFailure;
use PhpSchool\PhpWorkshop\Result\Cgi\RequestFailure;
use PhpSchool\PhpWorkshop\Result\Cgi\ResultInterface as CgiResultInterface;
use PhpSchool\PhpWorkshop\Result\Cgi\Success;
use PhpSchool\PhpWorkshop\Result\ResultInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class SandboxedCgiRunner implements ExerciseRunnerInterface
{
    private string $phpLocation;

    public function __construct(
        private readonly CgiExercise&ExerciseInterface $exercise,
        private readonly EventDispatcher $eventDispatcher,
        private readonly PhpSandbox $sandbox,
    ) {
        $php = (new ExecutableFinder())->find('php-cgi');

        if (null === $php) {
            throw new RuntimeException('Could not load php-cgi binary. Please install php using your package manager.');
        }

        $this->phpLocation = $php;
    }

    public function getName(): string
    {
        return 'CGI Program Runner';
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
        $this->eventDispatcher->dispatch(new ExerciseRunnerEvent('cgi.verify.start', $this->exercise, $input));
        $result = new CgiResult(
            array_map(
                fn(RequestInterface $request) => $this->checkRequest($request, $input),
                $this->exercise->getRequests()
            )
        );
        $this->eventDispatcher->dispatch(new ExerciseRunnerEvent('cgi.verify.finish', $this->exercise, $input));
        return $result;
    }

    public function run(Input $input, OutputInterface $output): bool
    {
        $this->eventDispatcher->dispatch(new ExerciseRunnerEvent('cgi.run.start', $this->exercise, $input));
        $success = true;
        foreach ($this->exercise->getRequests() as $request) {
            /** @var CgiExecuteEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new CgiExecuteEvent('cgi.run.student-execute.pre', $request)
            );
            $process = $this->getStudentProcess($input, $event->getRequest());

            $process->start();
            $this->eventDispatcher->dispatch(
                new CgiExecuteEvent('cgi.run.student.executing', $request, ['output' => $output])
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
                new CgiExecuteEvent('cgi.run.student-execute.post', $request)
            );
        }
        $this->eventDispatcher->dispatch(new ExerciseRunnerEvent('cgi.run.finish', $this->exercise, $input));
        return $success;
    }

    private function checkRequest(RequestInterface $request, Input $input): CgiResultInterface
    {
        try {
            /** @var CgiExecuteEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new CgiExecuteEvent('cgi.verify.reference-execute.pre', $request)
            );
            $solutionResponse = $this->execute(
                $this->getProcess(
                    $this->exercise->getSolution()->getEntryPoint()->getAbsolutePath(),
                    $event->getRequest(),
                    []
                ),
                $event->getRequest(),
                'reference'
            );
        } catch (CodeExecutionException $e) {
            $this->eventDispatcher->dispatch(new Event('cgi.verify.reference-execute.fail', ['exception' => $e]));
            throw new SolutionExecutionException($e->getMessage());
        }

        try {
            /** @var CgiExecuteEvent $event */
            $event = $this->eventDispatcher->dispatch(new CgiExecuteEvent('cgi.verify.student-execute.pre', $request));
            $userResponse = $this->execute(
                $this->getStudentProcess($input, $event->getRequest()),
                $event->getRequest(),
                'student'
            );
        } catch (CodeExecutionException $e) {
            $this->eventDispatcher->dispatch(new Event('cgi.verify.student-execute.fail', ['exception' => $e]));
            return GenericFailure::fromRequestAndCodeExecutionFailure($request, $e);
        }

        $solutionBody = (string) $solutionResponse->getBody();
        $userBody = (string) $userResponse->getBody();
        $solutionHeaders = $this->getHeaders($solutionResponse);
        $userHeaders = $this->getHeaders($userResponse);

        if ($solutionBody !== $userBody || $solutionHeaders !== $userHeaders) {
            return new RequestFailure($request, $solutionBody, $userBody, $solutionHeaders, $userHeaders);
        }

        return new Success($request);
    }

    /**
     * @return array<string, string>
     */
    private function getHeaders(ResponseInterface $response): array
    {
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }
        return $headers;
    }

    private function execute(Process $process, RequestInterface $request, string $type): ResponseInterface
    {
        $process->start();
        $this->eventDispatcher->dispatch(new CgiExecuteEvent(sprintf('cgi.verify.%s.executing', $type), $request));
        $process->wait();

        if (!$process->isSuccessful()) {
            throw CodeExecutionException::fromProcess($process);
        }

        $output = $process->getOutput();
        if (!preg_match('/^HTTP\/([1-9]\d*\.\d) ([1-5]\d{2})(\s+(.+))?\\r\\n/', $output)) {
            $output = "HTTP/1.0 200 OK\r\n" . $output;
        }

        return Message::parseResponse($output);
    }

    public function getStudentProcess(Input $input, RequestInterface $request): Process
    {
        $fileName = $input->getRequiredArgument('program');
        $root = $input->getArgument(SandboxedCliRunner::ROOT_ARGUMENT) ?? dirname($fileName);

        return $this->getProcess($fileName, $request, $this->sandbox->iniArguments($root));
    }

    /**
     * @param list<string> $iniArguments
     */
    private function getProcess(string $fileName, RequestInterface $request, array $iniArguments): Process
    {
        $env = [
            'REQUEST_METHOD' => $request->getMethod(),
            'SCRIPT_FILENAME' => $fileName,
            'REDIRECT_STATUS' => 302,
            'QUERY_STRING' => $request->getUri()->getQuery(),
            'REQUEST_URI' => $request->getUri()->getPath(),
            'XDEBUG_MODE' => 'off',
            'CONTENT_LENGTH' => (string) $request->getBody()->getSize(),
            'CONTENT_TYPE' => $request->getHeaderLine('Content-Type'),
        ];

        foreach ($request->getHeaders() as $name => $values) {
            $env[sprintf('HTTP_%s', strtoupper((string) $name))] = implode(', ', $values);
        }

        return new Process(
            [
                $this->phpLocation,
                '-dalways_populate_raw_post_data=-1',
                '-dhtml_errors=0',
                '-dexpose_php=0',
                ...$iniArguments,
            ],
            dirname($fileName),
            $this->sandbox->environment($env),
            $request->getBody()->__toString(),
            10
        );
    }
}
