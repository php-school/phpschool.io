<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\DownloadManager;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RuntimeException;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class TrackDownloads
{
    /**
     * @var DownloadManager
     */
    private $downloadManager;

    /**
     * @var WorkshopRepository
     */
    private $workshopRepository;

    public function __construct(WorkshopRepository $workshopRepository, DownloadManager $downloadManager)
    {
        $this->downloadManager = $downloadManager;
        $this->workshopRepository = $workshopRepository;
    }
    
    public function __invoke(Request $request, Response $response, $workshop, $version) : Response
    {
        try {
            $workshop = $this->workshopRepository->findByDisplayName($workshop);
        } catch (RuntimeException $e) {
            return new JsonResponse(
                ['status' => 'error', 'message' => sprintf('Workshop: "%s" not found.', $workshop)]
            );
        }

        $this->downloadManager->addInstall($workshop, $request->getAttribute('ip_address'), $version);

        return new JsonResponse(['status' => 'success'], 201);
    }
}
