<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\User\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListWorkshops
{
    private CloudWorkshopRepository $installedWorkshops;

    public function __construct(CloudWorkshopRepository $installedWorkshops)
    {
        $this->installedWorkshops = $installedWorkshops;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer): Response
    {
        return $renderer->render($response, 'layouts/cloud.phtml', [
            'pageTitle'       => 'PHP School Cloud',
            'pageDescription' => 'PHP School Cloud',
            'content'         => $renderer->fetch('cloud/list-workshops.phtml', [
                'totalExerciseCount' => $this->installedWorkshops->totalExerciseCount(),
                'workshops' => $this->installedWorkshops->findAll(),
            ])
        ]);
    }
}
