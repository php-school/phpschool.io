<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use RuntimeException;

class View
{
    use JsonUtils;

    private WorkshopRepository $repository;
    private WorkshopInstallRepository $workshopInstallRepository;
    private PhpRenderer $renderer;

    public function __construct(
        WorkshopRepository $repository,
        WorkshopInstallRepository $workshopInstallRepository,
        PhpRenderer $renderer
    ) {
        $this->repository = $repository;
        $this->workshopInstallRepository = $workshopInstallRepository;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer, string $id): MessageInterface
    {
        try {
            $workshop = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $this->withJson(
                [
                    'error' => 'Could not find workshop with id: ' . $id
                ],
                $response,
                500
            );
        }

        $installs = $this->workshopInstallRepository->findInstallsInLast30Days($workshop);

        return $this->withJson([
            'workshop' => $workshop,
            'installs' => $installs,
            'graphData' => $this->getLast30DayInstallGraphData($installs)
        ], $response);
    }

    private function getLast30DayInstallGraphData(array $installs): array
    {
        $end = new DateTimeImmutable();
        $begin = $end->sub(new DateInterval("P30D"));
        $end = $end->add(new DateInterval("P1D"));

        $interval   = new DateInterval('P1D');
        $dateRange  = new DatePeriod($begin, $interval, $end);

        $data = array_map(function (\DateTimeImmutable $dateTime) use ($installs) {
            return count(array_filter($installs, function (WorkshopInstall $install) use ($dateTime) {
                return $install->getDateTime()->format('d-m-Y') === $dateTime->format('d-m-Y');
            }));
        }, iterator_to_array($dateRange));

        $dates = array_map(function (\DateTimeImmutable $dateTime) {
            return $dateTime->format('d M');
        }, iterator_to_array($dateRange));

        return ['dates' => $dates, 'data' => $data];
    }
}
