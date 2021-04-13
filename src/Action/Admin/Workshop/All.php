<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class All
{
    /**
     * @var WorkshopRepository
     */
    private $repository;

    /**
     * @var WorkshopInstallRepository
     */
    private $workshopInstallRepository;

    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(
        WorkshopRepository $repository,
        WorkshopInstallRepository $workshopInstallRepository,
        PhpRenderer $renderer
    ) {
        $this->renderer = $renderer;
        $this->workshopInstallRepository = $workshopInstallRepository;
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response)
    {
        $this->renderer->addJs('charts', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.1/Chart.bundle.min.js');

        $workshops = $this->repository->findAll();
        $dateRange = $this->getDateRange();
        $inner = $this->renderer->fetch('admin/workshop/all.phtml', [
            'workshops' => $workshops,
            'workshopInstalls' => [
                'dates' => array_map(function (\DateTimeImmutable $dateTime) {
                    return $dateTime->format('d M');
                }, $dateRange),
                'installs' => array_map(function (Workshop $workshop) use ($dateRange) {
                    return [
                        'name' => $workshop->getDisplayName(),
                        'installs' => $this->getLast30DayInstallGraphData(
                            $this->workshopInstallRepository->findInstallsInLast30Days($workshop),
                            $dateRange
                        )
                    ];
                }, $workshops)
            ]
        ]);

        return $this->renderer->render($response, 'layouts/admin.phtml', [
            'pageTitle'       => 'All Workshops',
            'pageDescription' => 'All Workshops',
            'content'         => $inner
        ]);
    }

    private function getDateRange(): array
    {
        $end    = new DateTimeImmutable();
        $begin  = $end->sub(new DateInterval("P30D"));
        $end    = $end->add(new DateInterval("P1D"));

        $interval   = new DateInterval('P1D');
        return iterator_to_array(new DatePeriod($begin, $interval, $end));
    }

    private function getLast30DayInstallGraphData(array $installs, array $dateRange): array
    {
        return array_map(function (DateTimeImmutable $dateTime) use ($installs) {
            return count(array_filter($installs, function (WorkshopInstall $install) use ($dateTime) {
                return $install->getDateTime()->format('d-m-Y') === $dateTime->format('d-m-Y');
            }));
        }, $dateRange);
    }
}
