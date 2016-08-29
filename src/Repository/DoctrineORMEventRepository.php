<?php

namespace PhpSchool\Website\Repository;

use Doctrine\ORM\EntityRepository;
use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\Entity\Workshop;
use RuntimeException;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class DoctrineORMEventRepository extends EntityRepository implements EventRepository
{

    /**
     * @param int $limit
     * @return Event[]
     */
    public function findPrevious($limit = 10) : array
    {
        return $this->createQueryBuilder('e')
            ->where('e.dateTime < :now')
            ->orderBy('e.dateTime', 'DESC')
            ->setMaxResults($limit)
            ->setParameter(':now', new \DateTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $limit
     * @return Event[]
     */
    public function findUpcoming($limit = 10) : array
    {
        return $this->createQueryBuilder('e')
            ->where('e.dateTime > :now')
            ->orderBy('e.dateTime', 'ASC')
            ->setMaxResults($limit)
            ->setParameter(':now', new \DateTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[]
     */
    public function findAll() : array
    {
        return parent::findBy([], ['dateTime' => 'DESC']);
    }

    /**
     * @param string $id
     * @return Event
     * @throws RuntimeException
     */
    public function findById(string $id) : Event
    {
        $event = parent::find($id);

        if (null !== $event) {
            return $event;
        }
        throw new RuntimeException(sprintf('Cannot find event with id: "%s"', $id));
    }

    public function save(Event $event)
    {
        $this->_em->persist($event);
        $this->_em->flush();
    }

    public function remove(Event $event)
    {
        $this->_em->remove($event);
        $this->_em->flush();
    }
}
