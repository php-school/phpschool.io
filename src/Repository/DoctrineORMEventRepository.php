<?php

declare(strict_types=1);

namespace PhpSchool\Website\Repository;

use Doctrine\ORM\EntityRepository;
use PhpSchool\Website\Entity\Event;
use RuntimeException;

/**
 * @template-extends EntityRepository<Event>
 */
class DoctrineORMEventRepository extends EntityRepository implements EventRepository
{
    /**
     * @return list<Event>
     */
    public function findPrevious(int $limit = 10): array
    {
        /** @var list<Event> $result */
        $result = $this->createQueryBuilder('e')
            ->where('e.dateTime <= :now')
            ->orderBy('e.dateTime', 'DESC')
            ->setMaxResults($limit)
            ->setParameter(':now', new \DateTime())
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return list<Event>
     */
    public function findUpcoming(int $limit = 10): array
    {
        /** @var list<Event> $result */
        $result = $this->createQueryBuilder('e')
            ->where('e.dateTime > :now')
            ->orderBy('e.dateTime', 'ASC')
            ->setMaxResults($limit)
            ->setParameter(':now', new \DateTime())
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return list<Event>
     */
    public function findAll(): array
    {
        return parent::findBy([], ['dateTime' => 'DESC']);
    }

    public function findById(string $id): Event
    {
        $event = parent::find($id);

        if (null !== $event) {
            return $event;
        }
        throw new RuntimeException(sprintf('Cannot find event with id: "%s"', $id));
    }

    public function save(Event $event): void
    {
        $this->_em->persist($event);
        $this->_em->flush();
    }

    public function remove(Event $event): void
    {
        $this->_em->remove($event);
        $this->_em->flush();
    }
}
