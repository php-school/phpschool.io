<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Online;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Workshops
{
    use JsonUtils;

    public function __construct(
        private readonly CloudWorkshopRepository $installedWorkshops,
        private readonly WorkshopRepository $workshopRepository,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->withJson(
            [
                'onlineWorkshops' => $this->installedWorkshops->findAll(),
                //public, so only approved workshops and never the submitter's email
                'allWorkshops' => array_map(
                    fn(Workshop $workshop) => [
                        'code' => $workshop->getCode(),
                        'name' => $workshop->getDisplayName(),
                        'description' => $workshop->getDescription(),
                        'repo_url' => $workshop->getRepoUrl(),
                        'submitter_name' => $workshop->getSubmitterName(),
                        //older submissions weren't checked, eg "javascript:..."
                        'submitter_contact' => preg_match('#^https?://#i', (string) $workshop->getSubmitterContact())
                            ? $workshop->getSubmitterContact()
                            : null,
                        'type' => $workshop->getTypeName(),
                    ],
                    $this->workshopRepository->findAllApproved(),
                ),
                'totalExercises' => $this->installedWorkshops->totalExerciseCount(),
            ],
            $response,
        );
    }
}
