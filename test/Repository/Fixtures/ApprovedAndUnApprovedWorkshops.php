<?php

namespace PhpSchool\WebsiteTest\Repository\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSchool\Website\Entity\Workshop;
use Ramsey\Uuid\UuidInterface;

class ApprovedAndUnApprovedWorkshops implements FixtureInterface
{
    public UuidInterface $php8appreciateId;
    public UuidInterface $learnYouPhpId;

    public function load(ObjectManager $manager)
    {
        $approvedWorkshop = new Workshop(
            'php-school',
            'php8-appreciate',
            'php8appreciate',
            'PHP 8 Appreciate',
            'PHP 8 Workshop',
            'aydin@hotmail.co.uk',
            'Aydin Hassan',
            'https://twitter.com/aydin_h',
            true
        );

        $unApprovedWorkshop = new Workshop(
            'php-school',
            'learn-you-php',
            'learnyouphp',
            'Learn You PHP',
            'PHP Basics',
            'aydin@hotmail.co.uk',
            'Aydin Hassan',
            'https://twitter.com/aydin_h',
            false
        );

        $manager->persist($approvedWorkshop);
        $manager->persist($unApprovedWorkshop);
        $manager->flush();

        $this->php8appreciateId = $approvedWorkshop->getId();
        $this->learnYouPhpId = $unApprovedWorkshop->getId();
    }
}
