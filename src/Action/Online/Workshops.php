<?php

namespace PhpSchool\Website\Action\Online;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Workshops
{
    use JsonUtils;

    public function __construct(private readonly CloudWorkshopRepository $installedWorkshops)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->withJson(
            [
                'workshops' => $this->installedWorkshops->findAll(),
                'totalExercises' => $this->installedWorkshops->totalExerciseCount(),
            ],
            $response
        );
    }
}
