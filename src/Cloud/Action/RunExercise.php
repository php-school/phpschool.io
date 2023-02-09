<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\PhpWorkshop\Event\CgiExecuteEvent;
use PhpSchool\PhpWorkshop\Event\CliExecuteEvent;
use PhpSchool\PhpWorkshop\Event\Event;
use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\Output\BufferedOutput;
use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\UploadProject;
use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RunExercise
{
    use JsonUtils;

    private CloudWorkshopRepository $installedWorkshops;
    private ExerciseDispatcher $exerciseDispatcher;
    private array $runInfo = [];

    public function __construct(CloudWorkshopRepository $installedWorkshops, ExerciseDispatcher $exerciseDispatcher)
    {
        $this->installedWorkshops = $installedWorkshops;
        $this->exerciseDispatcher = $exerciseDispatcher;
    }

    public function __invoke(
        Request $request,
        Response $response,
        PhpRenderer $renderer,
        string $workshop,
        string $exercise
    ): Response {
        $workshop = $this->installedWorkshops->findByCode($workshop);
        $exercise = $workshop->findExerciseBySlug($exercise);

        $basePath = ($project = new UploadProject())->upload($request);

        $output = new BufferedOutput();

        $workshop->getExerciseDispatcher()
            ->getEventDispatcher()
            ->listen(['cli.run.student-execute.post', 'cgi.run.student-execute.post'], function (Event $event) use ($output) {
                $this->collectRunInfo($event, $output);
            });

        $result = $workshop->getExerciseDispatcher()->run(
            $exercise,
            new Input($workshop->getCode(), ['program' => Path::join($basePath, $project->getEntryPoint())]),
            $output
        );

        $data = [
            'runs' => $this->runInfo,
            'success' => $result
        ];

        $this->runInfo = [];

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
