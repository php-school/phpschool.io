<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Online;

use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Online\ProjectUploader;
use PhpSchool\Website\Online\StudentWorkshopState;
use PhpSchool\Website\Online\VueResultsRenderer;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Filesystem\Filesystem;

class VerifyExercise
{
    use JsonUtils;

    public function __construct(
        private readonly CloudWorkshopRepository $installedWorkshops,
        private readonly ProjectUploader $projectUploader,
        private readonly SessionStorageInterface $session,
        private readonly StudentWorkshopState $studentState,
        private readonly VueResultsRenderer $resultsRenderer
    ) {}

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

        $this->studentState->setCurrentExercise($workshop->getCode(), $exercise->getName());

        try {
            $project = $this->projectUploader->upload($request, $this->getStudent());
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

    private function getStudent(): StudentDTO
    {
        $student = $this->session->get('student');

        if (!$student instanceof StudentDTO) {
            throw new \RuntimeException('Needs a logged in user');
        }

        return $student;
    }
}
