<?php

namespace PhpSchool\Website\Action\Online;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Online\StudentWorkshopState;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ResetStateFromEditor
{
    use JsonUtils;

    public function __construct(
        private readonly CloudWorkshopRepository $installedWorkshops,
        private readonly StudentWorkshopState $studentState,
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

        $this->studentState->reset();
        $this->studentState->setCurrentExercise($workshop->getCode(), $exercise->getName());

        return $this->jsonSuccess($response);
    }
}
