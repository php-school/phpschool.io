<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Dashboard
{
    private CloudWorkshopRepository $installedWorkshops;

    public function __construct(CloudWorkshopRepository $installedWorkshops)
    {
        $this->installedWorkshops = $installedWorkshops;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer): Response
    {
        $content = sprintf(
            "<dashboard %s :total-exercises='%s' :workshops='%s' :links='%s'/>",
            $renderer->getAttribute('student') ? sprintf(":student='%s'", $renderer->json($renderer->getAttribute('student'))) : "",
            $renderer->json($this->installedWorkshops->totalExerciseCount()),
            $renderer->json($this->installedWorkshops->findAll()),
            $renderer->json($renderer->getAttribute('links'))
        );

        return $renderer->render($response, 'layouts/cloud.phtml', [
            'pageTitle' => 'PHP School Cloud',
            'pageDescription' => 'PHP School Cloud',
            'content' => $content
        ]);
    }
}
