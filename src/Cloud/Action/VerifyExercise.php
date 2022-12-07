<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\UserState;
use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\PhpWorkshop\Utils\System;
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

        $basePath = System::tempDir(); //TODO: unique per user
        foreach ($data['scripts'] ?? [] as $filePath => $content) {
            $fileName = basename($filePath);
            $path = dirname($filePath);

            //TODO: directory reversal hacks
            if (!file_exists(Path::join($basePath, $path))) {
                mkdir(Path::join($basePath, $path), 0777, true); //TODO: must not do 0777
            }

            file_put_contents(Path::join($basePath, $path, $fileName), $content);
        }

        $results = $workshop->getExerciseDispatcher()->verify(
            $exercise,
            new Input($workshop->getCode(), ['program' => Path::join($basePath, 'solution.php')]),
        );

        $renderer = new ResultsRenderer();

        $data = [
            'results' => $renderer->render($results, $exercise, new UserState()),
            'success' => $results->isSuccessful()
        ];

        return $this->withJson($data, $response);
    }
}
