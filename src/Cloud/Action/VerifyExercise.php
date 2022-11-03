<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\UserState;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\PhpWorkshop\ResultsRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class VerifyExercise
{
    use JsonUtils;

    private CloudWorkshopRepository $installedWorkshops;
    private ExerciseDispatcher $exerciseDispatcher;

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
        $renderer->appendLocalCss('tailwindcss', __DIR__ . '/../../../public/css/cloud.min.css');

        $workshop = $this->installedWorkshops->findByCode($workshop);
        $exercise = $workshop->findExerciseByName($exercise);

        $data = json_decode($request->getBody()->__toString(), true);
        $script = $data['script'];

        $tmp = tempnam(sys_get_temp_dir(), 'phpschool');

        file_put_contents($tmp, $script);

        $results = $workshop->getExerciseDispatcher()->verify(
            $exercise,
            new Input($workshop->getCode(), ['program' => $tmp])
        );

        $renderer = new ResultsRenderer();

        $data = [
            'results' => $renderer->render($results, $exercise, new UserState()),
            'success' => $results->isSuccessful()
        ];

        if ($results->isSuccessful()) {
            $nextExercise = $workshop->findNextExercise($exercise);

            if ($nextExercise !== null) {
                $data['next_exercise'] = $nextExercise->getName();
            }
        }

        return $this->withJson($data, $response);
    }
}
