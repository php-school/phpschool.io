<?php

namespace PhpSchool\Website\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;

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

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }

    public function totalInstalls(Workshop $workshop): int
    {
        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(*)')
            ->where('s.workshop = :workshop')
            ->setParameter('workshop', $workshop);

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }

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

        return $qb->getQuery()->getResult();
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
