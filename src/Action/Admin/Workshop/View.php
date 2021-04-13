<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use RuntimeException;

class View
{
    use RedirectUtils;

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

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer, string $id): Response
    {
        try {
            $workshop = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $this->redirect('/admin/workshop/all');
        }

        $this->renderer->addJs('charts', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.1/Chart.bundle.min.js');

        $installs = $this->workshopInstallRepository->findInstallsInLast30Days($workshop);
        $inner = $this->renderer->fetch('admin/workshop/view.phtml', [
            'workshop'  => $workshop,
            'installs'  => $installs,
            'graphData' => $this->getLast30DayInstallGraphData($installs)
        ]);

        return $this->renderer->render($response, 'layouts/admin.phtml', [
            'pageTitle'       => $workshop->getDisplayName(),
            'pageDescription' => $workshop->getDisplayName(),
            'content'         => $inner
        ]);
    }

    private function getLast30DayInstallGraphData(array $installs): array
    {
        $end    = new DateTimeImmutable();
        $begin  = $end->sub(new DateInterval("P30D"));
        $end    = $end->add(new DateInterval("P1D"));

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
