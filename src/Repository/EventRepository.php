<?php

declare(strict_types=1);

namespace PhpSchool\Website\Repository;

use PhpSchool\Website\Entity\Event;

interface EventRepository
{
    public function findById(string $id): Event;

    /**
     * @return list<Event>
     */
    public function findPrevious(int $limit = 10): array;

    /**
     * @return list<Event>
     */
    public function findUpcoming(int $limit = 10): array;

    /**
     * @return list<Event>
     */
    public function findAll(): array;

    public function save(Event $event): void;

    public function remove(Event $event): void;
}
