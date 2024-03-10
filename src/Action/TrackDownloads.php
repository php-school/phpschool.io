<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RuntimeException;

class TrackDownloads
{
    use JsonUtils;

    private WorkshopRepository $workshopRepository;
    private WorkshopInstallRepository $workshopInstallRepository;

    public function __construct(
        WorkshopRepository $workshopRepository,
        WorkshopInstallRepository $workshopInstallRepository
    ) {
        $this->workshopRepository = $workshopRepository;
        $this->workshopInstallRepository = $workshopInstallRepository;
    }

    public function __invoke(Request $request, Response $response, string $workshop, string $version): Response
    {
        try {
            $workshopEntity = $this->workshopRepository->findByCode($workshop);
        } catch (RuntimeException $e) {
            return $this
                ->withJson(['status' => 'error', 'message' => "Workshop: \"$workshop\" not found."], $response, 404);
        }

        /** @var string $ipAddress */
        $ipAddress = $request->getAttribute('ip_address');

        $this->workshopInstallRepository->save(
            new WorkshopInstall($workshopEntity, $ipAddress, $version)
        );

        return $this->jsonSuccess($response);
    }
}
