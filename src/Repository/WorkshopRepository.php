<?php

declare(strict_types=1);

namespace PhpSchool\Website\Repository;

use PhpSchool\Website\Entity\Workshop;

interface WorkshopRepository
{
    /**
     * @return list<Workshop>
     */
    public function findAllPendingApproval(): array;

    /**
     * @return list<Workshop>
     */
    public function findAllApproved(): array;

    /**
     * @return list<Workshop>
     */
    public function findAll(): array;

    public function findById(string $id): Workshop;

    public function findByDisplayName(string $displayName): Workshop;

    public function findByCode(string $name): Workshop;

    public function save(Workshop $workshopSubmission): void;

    public function remove(Workshop $workshop): void;
}
