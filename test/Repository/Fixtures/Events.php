<?php

namespace PhpSchool\WebsiteTest\Repository\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\Entity\Workshop;
use Ramsey\Uuid\UuidInterface;

class Events implements FixtureInterface
{
    public UuidInterface $event1Id;

    public function load(ObjectManager $manager)
    {
        $date = new \DateTime('5 days ago');

        $manager->persist(
            $e = new Event('Event 1', 'Event 1', null, clone $date->add(new \DateInterval('P1D')), 'Location 1', null)
        );

        $this->event1Id = $e->getId();

        $manager->persist(
            new Event('Event 2', 'Event 2', null, clone $date->add(new \DateInterval('P1D')), 'Location 2', null)
        );

        $manager->persist(
            new Event('Event 3', 'Event 3', null, clone $date->add(new \DateInterval('P1D')), 'Location 3', null)
        );

        $manager->persist(
            new Event('Event 4', 'Event 4', null, clone $date->add(new \DateInterval('P1D')), 'Location 4', null)
        );

        $manager->persist(
            new Event('Event 5', 'Event 5', null, clone $date->add(new \DateInterval('P1D')), 'Location 5', null)
        );

        $manager->persist(
            new Event('Event 6', 'Event 6', null, clone $date->add(new \DateInterval('P1D')), 'Location 6', null)
        );

        $manager->persist(
            new Event('Event 7', 'Event 7', null, clone $date->add(new \DateInterval('P1D')), 'Location 7', null)
        );

        $manager->persist(
            new Event('Event 8', 'Event 8', null, clone $date->add(new \DateInterval('P1D')), 'Location 8', null)
        );

        $manager->persist(
            new Event('Event 9', 'Event 9', null, clone $date->add(new \DateInterval('P1D')), 'Location 9', null)
        );

        $manager->persist(
            new Event('Event 10', 'Event 10', null, clone $date->add(new \DateInterval('P1D')), 'Location 10', null)
        );

        $manager->flush();
    }
}
