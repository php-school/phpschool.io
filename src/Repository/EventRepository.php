<?php

namespace PhpSchool\Website\Repository;

use PhpSchool\Website\Entity\Event;
use RuntimeException;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
interface EventRepository
{

    /**
     * @param string $id
     * @return Event
     * @throws RuntimeException
     */
    public function findById(string $id) : Event;

    /**
     * @param int $limit
     * @return Event[]
     */
    public function findPrevious($limit = 10) : array;

    /**
     * @param int $limit
     * @return Event[]
     */
    public function findUpcoming($limit = 10) : array;

    /**
     * @return Event[]
     */
    public function findAll() : array;

    public function save(Event $event);

    public function remove(Event $event);
}
