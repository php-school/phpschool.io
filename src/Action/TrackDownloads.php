<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\DownloadManager;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RuntimeException;
use Slim\Http\Response as SlimResponse;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class TrackDownloads
{
    /**
     * @var WorkshopRepository
     */
    private $workshopRepository;

    /**
     * @var WorkshopInstallRepository
     */
    private $workshopInstallRepository;

    public function __construct(
        WorkshopRepository $workshopRepository,
        WorkshopInstallRepository $workshopInstallRepository
    ) {
        $this->workshopRepository = $workshopRepository;
        $this->workshopInstallRepository = $workshopInstallRepository;
    }

    public function __invoke(Request $request, Response $response, $workshop, $version) : Response
    {
        try {
            $workshop = $this->workshopRepository->findByCode($workshop);
        } catch (RuntimeException $e) {
            return (new SlimResponse())
                ->withJson(['status' => 'error', 'message' => sprintf('Workshop: "%s" not found.', $workshop)], 404);
        }

        $this->workshopInstallRepository->save(
            new WorkshopInstall($workshop, $request->getAttribute('ip_address'), $version)
        );

        return (new SlimResponse())->withJson(['status' => 'success'], 201);
    }
}
