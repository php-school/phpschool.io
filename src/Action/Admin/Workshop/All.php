<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class All
{
    use JsonUtils;

    private WorkshopRepository $repository;
    private WorkshopInstallRepository $workshopInstallRepository;

    public function __construct(
        WorkshopRepository $repository,
        WorkshopInstallRepository $workshopInstallRepository,
    ) {
        $this->workshopInstallRepository = $workshopInstallRepository;
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $workshops = $this->repository->findAll();
        $dateRange = $this->getDateRange();

        return $this->withJson([
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
            ],
        ], $response);
    }

    /**
     * @return array<DateTimeImmutable>
     */
    private function getDateRange(): array
    {
        $end = new DateTimeImmutable();
        $begin = $end->sub(new DateInterval("P30D"));
        $end  = $end->add(new DateInterval("P1D"));

        $interval   = new DateInterval('P1D');
        return iterator_to_array(new DatePeriod($begin, $interval, $end));
    }

    /**
     * @param array<WorkshopInstall> $installs
     * @param array<DateTimeImmutable> $dateRange
     * @return array<int>
     */
    private function getLast30DayInstallGraphData(array $installs, array $dateRange): array
    {
        return array_map(
            function (DateTimeImmutable $dateTime) use ($installs) {
                return count(array_filter($installs, function (WorkshopInstall $install) use ($dateTime) {
                    return $install->getDateTime()->format('d-m-Y') === $dateTime->format('d-m-Y');
                }));
            },
            $dateRange
        );
    }
}
