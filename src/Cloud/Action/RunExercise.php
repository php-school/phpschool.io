<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\PhpWorkshop\Event\CgiExecuteEvent;
use PhpSchool\PhpWorkshop\Event\CliExecuteEvent;
use PhpSchool\PhpWorkshop\Event\Event;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\Output\BufferedOutput;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\ProjectUploader;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Filesystem\Filesystem;

class RunExercise
{
    use JsonUtils;

    private array $runInfo = [];

    public function __construct(
        private readonly CloudWorkshopRepository $installedWorkshops,
        private readonly ProjectUploader $projectUploader,
    ) {
    }

    public function __invoke(
        Request $request,
        Response $response,
        string $workshop,
        string $exercise
    ): Response {
        try {
            $workshop = $this->installedWorkshops->findByCode($workshop);
            $exercise = $workshop->findExerciseBySlug($exercise);
        } catch (\RuntimeException $e) {
            return $this->withJson(['success' => false, 'error' => $e->getMessage()], $response);
        }

        try {
            $project = $this->projectUploader->upload($request);
        } catch (\RuntimeException $e) {
            return $this->withJson(['success' => false, 'error' => $e->getMessage()], $response);
        }

        $output = new BufferedOutput();

        $workshop->getExerciseDispatcher()
            ->getEventDispatcher()
            ->listen(['cli.run.student-execute.post', 'cgi.run.student-execute.post'], function (Event $event) use ($output) {
                $this->collectRunInfo($event, $output);
            });

        $result = $workshop->getExerciseDispatcher()->run(
            $exercise,
            new Input($workshop->getCode(), ['program' => $project->getEntryPoint()->getAbsolutePath()]),
            $output
        );

        $data = [
            'runs' => $this->runInfo,
            'success' => $result
        ];

        $this->runInfo = [];

        (new Filesystem())->remove($project->getBaseDirectory());

        return $this->withJson($data, $response);
    }

    private function collectRunInfo(Event $event, BufferedOutput $output): void
    {
        switch (get_class($event)) {
            case CliExecuteEvent::class:
                $this->runInfo[] = [
                    'args' => $event->getArgs()->getArrayCopy(),
                    'output' => trim($output->fetch(true))
                ];
                break;
            case CgiExecuteEvent::class:
                $this->runInfo[] = [
                    'request' => [
                        'method' => $event->getRequest()->getMethod(),
                        'uri' => $event->getRequest()->getUri()->__toString(),
                        'headers' => $event->getRequest()->getHeaders(),
                        'body' => $event->getRequest()->getBody()->__toString(),
                    ],
                    'output' => trim($output->fetch(true))
                ];
                break;
        }
    }
}
