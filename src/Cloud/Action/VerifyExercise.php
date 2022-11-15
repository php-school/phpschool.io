<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\UserState;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\ResultsRenderer;
use PhpSchool\Website\PhpRenderer;
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
        $workshop = $this->installedWorkshops->findByCode($workshop);
        $exercise = $workshop->findExerciseBySlug($exercise);

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

        return $this->withJson($data, $response);
    }
}