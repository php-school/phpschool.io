<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Online;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Workshops
{
    use JsonUtils;

    public function __construct(
        private readonly CloudWorkshopRepository $installedWorkshops,
        private readonly WorkshopRepository $workshopRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->withJson(
            [
                'onlineWorkshops' => $this->installedWorkshops->findAll(),
                'allWorkshops' => $this->workshopRepository->findAll(),
                'totalExercises' => $this->installedWorkshops->totalExerciseCount(),
            ],
            $response
        );
    }
}
