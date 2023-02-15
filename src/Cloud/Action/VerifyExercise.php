<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\ProjectUploader;
use PhpSchool\Website\Cloud\StudentWorkshopState;
use PhpSchool\Website\Cloud\VueResultsRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Filesystem\Filesystem;

class VerifyExercise
{
    use JsonUtils;

    public function __construct(
        private readonly CloudWorkshopRepository $installedWorkshops,
        private readonly ProjectUploader $projectUploader,
        private readonly StudentWorkshopState $studentState,
        private readonly VueResultsRenderer $resultsRenderer
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

        $results = $workshop->getExerciseDispatcher()->verify(
            $exercise,
            new Input($workshop->getCode(), ['program' => $project->getEntryPoint()->getAbsolutePath()]),
        );

        $data = [
            'results' => $this->resultsRenderer->render($results, $exercise),
            'success' => $results->isSuccessful()
        ];

        if ($results->isSuccessful()) {
            $this->studentState->completeExercise(
                $workshop->getCode(),
                $exercise->getName()
            );
        }

        //clean up
        (new Filesystem())->remove($project->getBaseDirectory());

        return $this->withJson($data, $response);
    }
}
