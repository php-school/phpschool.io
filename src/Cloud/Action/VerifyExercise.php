<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\UserState\UserState;
use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\ResultsRenderer;
use PhpSchool\Website\Cloud\StudentWorkshopState;
use PhpSchool\Website\Cloud\UploadProject;
use PhpSchool\Website\Cloud\VueResultsRenderer;
use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Filesystem\Filesystem;

class VerifyExercise
{
    use JsonUtils;

    private CloudWorkshopRepository $installedWorkshops;
    private ExerciseDispatcher $exerciseDispatcher;
    private StudentWorkshopState $studentState;

    public function __construct(
        CloudWorkshopRepository $installedWorkshops,
        ExerciseDispatcher $exerciseDispatcher,
        StudentWorkshopState $studentState,
    ) {
        $this->installedWorkshops = $installedWorkshops;
        $this->exerciseDispatcher = $exerciseDispatcher;
        $this->studentState = $studentState;
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

        $results = $workshop->getExerciseDispatcher()->verify(
            $exercise,
            new Input($workshop->getCode(), ['program' => Path::join($basePath, $project->getEntryPoint())]),
        );

        $renderer = new VueResultsRenderer();

        $data = [
            'results' => $renderer->render($results, $exercise),
            'success' => $results->isSuccessful()
        ];

        if ($results->isSuccessful()) {
            $this->studentState->completeExercise(
                $workshop->getCode(),
                $exercise->getName()
            );
        }

        //clean up
        (new Filesystem())->remove($basePath);

        return $this->withJson($data, $response);
    }
}
