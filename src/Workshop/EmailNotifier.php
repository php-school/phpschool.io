<?php

declare(strict_types=1);

namespace PhpSchool\Website\Workshop;

use PhpSchool\Website\Entity\Workshop;
use SendGrid;

class EmailNotifier
{
    private SendGrid $sendGrid; /** @phpstan-ignore-line */
    private string $phpSchoolEmail; /** @phpstan-ignore-line */

    /**
     * @var array<string, string>
     */
    private array $templates = [ /** @phpstan-ignore-line */
        'Workshop Submitted' => 'c3c5b0c3-5059-4025-a986-7a2c763e80e8',
        'Workshop Approved' => '3f8709bd-d30d-4214-a6cd-8b92b74a6f21',
    ];


    public function __construct(SendGrid $sendGrid, string $phpSchoolEmail)
    {
        $this->sendGrid = $sendGrid;
        $this->phpSchoolEmail = $phpSchoolEmail;
    }

    public function approved(Workshop $workshop): void {}

    public function new(Workshop $workshop): void {}
}
