<?php

namespace PhpSchool\Website\Repository;

use Doctrine\ORM\EntityRepository;
use PhpSchool\Website\Entity\Workshop;
use RuntimeException;

/**
 * @template-extends EntityRepository<Workshop>
 */
class DoctrineORMWorkshopRepository extends EntityRepository implements WorkshopRepository
{
    /**
     * @return list<Workshop>
     */
    public function findAllPendingApproval(): array
    {
        return $this->findBy(['approved' => false]);
    }

    /**
     * @return list<Workshop>
     */
    public function findAllApproved(): array
    {
        return $this->findBy(['approved' => true]);
    }


    /**
     * @return list<Workshop>
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

    public function findById(string $id): Workshop
    {
        $workshop = parent::find($id);

        if (null !== $workshop) {
            return $workshop;
        }
        throw new RuntimeException(sprintf('Cannot find workshop with id: "%s"', $id));
    }

    public function findByDisplayName(string $displayName): Workshop
    {
        $workshop = parent::findOneBy(['displayName' => $displayName]);

        if (null !== $workshop) {
            return $workshop;
        }
        throw new RuntimeException(sprintf('Cannot find workshop with display name: "%s"', $displayName));
    }

    public function findByCode(string $name): Workshop
    {
        $workshop = parent::findOneBy(['code' => $name]);

        if (null !== $workshop) {
            return $workshop;
        }
        throw new RuntimeException(sprintf('Cannot find workshop with code: "%s"', $name));
    }

    public function save(Workshop $workshopSubmission): void
    {
        $this->_em->persist($workshopSubmission);
        $this->_em->flush();
    }

    public function remove(Workshop $workshop): void
    {
        $this->_em->remove($workshop);
        $this->_em->flush();
    }
}
