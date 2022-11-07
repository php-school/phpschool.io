<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\ProblemFileConverter;
use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ShowExerciseProblem
{
    private CloudWorkshopRepository $installedWorkshops;
    private ProblemFileConverter $problemFileConverter;

    public function __construct(
        CloudWorkshopRepository $installedWorkshops,
        ProblemFileConverter $problemFileConverter
    ) {
        $this->installedWorkshops = $installedWorkshops;
        $this->problemFileConverter = $problemFileConverter;
    }

    public function __invoke(
        Request $request,
        Response $response,
        PhpRenderer $renderer,
        string $workshop,
        string $exercise
    ): Response {

        $workshop = $this->installedWorkshops->findByCode($workshop);
        $exercise = $workshop->findExerciseByName($exercise);

        return $renderer->render($response, 'layouts/layout.phtml', [
            'pageTitle'       => 'PHP School Cloud',
            'pageDescription' => 'PHP School Cloud',
            'content'         => $renderer->fetch('cloud/exercise-problem.phtml', [
                'workshop' => $workshop,
                'exercise' => $exercise,
                'problem' => $this->problemFileConverter->htmlFromExercise($exercise)
            ])
        ]);
    }
}
