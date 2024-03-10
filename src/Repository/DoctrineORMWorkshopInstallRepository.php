<?php

declare(strict_types=1);

namespace PhpSchool\Website\Repository;

use Doctrine\ORM\EntityRepository;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;

/**
 * @extends EntityRepository<WorkshopInstall>
 */
class DoctrineORMWorkshopInstallRepository extends EntityRepository implements WorkshopInstallRepository
{
    public function totalInstallsInLast30Days(Workshop $workshop): int
    {
        $now            = new \DateTimeImmutable();
        $thirtyDaysAgo  = $now->sub(new \DateInterval("P30D"));
        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(*)');

        $where = $qb->expr()->between(
            's.dateTime',
            ':from',
            ':to'
        );

        $qb
            ->where($where)
            ->andWhere('s.workshop = :workshop')
            ->setParameters([
                'from' => $thirtyDaysAgo,
                'to' => $now,
                'workshop' => $workshop
            ]);

        /** @var ?int $result */
        $result = $qb->getQuery()->getSingleScalarResult();

        return  $result ?? 0;
    }

    public function totalInstalls(Workshop $workshop): int
    {
        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(*)')
            ->where('s.workshop = :workshop')
            ->setParameter('workshop', $workshop);

        /** @var ?int $result */
        $result = $qb->getQuery()->getSingleScalarResult();

        return  $result ?? 0;
    }

    /**
     * @return array<WorkshopInstall>
     */
    public function findInstallsInLast30Days(Workshop $workshop): array
    {
        $now            = new \DateTimeImmutable();
        $thirtyDaysAgo  = $now->sub(new \DateInterval("P30D"));
        $qb = $this->createQueryBuilder('s');
        $where = $qb->expr()->between(
            's.dateTime',
            ':from',
            ':to'
        );

        $qb
            ->where($where)
            ->andWhere('s.workshop = :workshop')
            ->setParameters([
                'from' => $thirtyDaysAgo,
                'to' => $now,
                'workshop' => $workshop
            ]);

        /** @var array<WorkshopInstall> $result */
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function save(WorkshopInstall $workshopInstall): void
    {
        $this->_em->persist($workshopInstall);
        $this->_em->flush();
    }

    public function removeAllByWorkshop(Workshop $workshop): void
    {
        $qb = $this->createQueryBuilder('s')
            ->delete(WorkshopInstall::class, 's')
            ->where('s.workshop = :workshop')
            ->setParameter('workshop', $workshop);

        $qb->getQuery()->execute();
    }
}
