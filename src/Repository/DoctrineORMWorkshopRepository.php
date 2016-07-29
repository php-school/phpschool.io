<?php

namespace PhpSchool\Website\Repository;

use Doctrine\ORM\EntityRepository;
use PhpSchool\Website\Entity\Workshop;
use RuntimeException;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class DoctrineORMWorkshopRepository extends EntityRepository implements WorkshopRepository
{

    /**
     * @return Workshop[]
     */
    public function findAllPendingApproval() : array
    {
        return $this->findBy(['approved' => false]);
    }

    /**
     * @return Workshop[]
     */
    public function findAll() : array
    {
        return parent::findAll();
    }

    /**
     * @param string $id
     * @return Workshop
     * @throws RuntimeException
     */
    public function findById(string $id) : Workshop
    {
        $workshop = parent::find($id);

        if (null !== $workshop) {
            return $workshop;
        }
        throw new RuntimeException(sprintf('Cannot find workshop with id: "%s"', $id));
    }

    /**
     * @param string $displayName
     * @return Workshop
     * @throws RuntimeException
     */
    public function findByDisplayName(string $displayName) : Workshop
    {
        $workshop = parent::findOneBy(['display' => $displayName]);

        if (null !== $workshop) {
            return $workshop;
        }
        throw new RuntimeException(sprintf('Cannot find workshop with display name: "%s"', $displayName));
    }

    /**
     * @param Workshop $workshopSubmission
     */
    public function save(Workshop $workshopSubmission)
    {
        $this->_em->persist($workshopSubmission);
        $this->_em->flush();
    }
}
