<?php

namespace PhpSchool\WebsiteTest\Repository;

use PhpSchool\Website\Entity\Event;
use PhpSchool\WebsiteTest\Repository\Fixtures\Events;

class DoctrineORMEventRepositoryTest extends DoctrineORMRepositoryTest
{
    public function testFindPreviousEventsReturnsOnlyEventsInThePast(): void
    {
        $this->loadFixture(new Events());

        $events = $this->getRepository(Event::class)->findPrevious();

        $this->assertCount(5, $events);

        $this->assertEquals(
            ['Event 5', 'Event 4', 'Event 3', 'Event 2', 'Event 1'],
            array_map(fn (Event $event) => $event->getName(), $events)
        );
    }

    public function testFindUpcomingEventsReturnsOnlyEventsInTheFuture(): void
    {
        $this->loadFixture(new Events());

        $events = $this->getRepository(Event::class)->findUpcoming();

        $this->assertCount(5, $events);

        $this->assertEquals(
            ['Event 6', 'Event 7', 'Event 8', 'Event 9', 'Event 10'],
            array_map(fn (Event $event) => $event->getName(), $events)
        );
    }

    public function testFindAll(): void
    {
        $this->loadFixture(new Events());

        $events = $this->getRepository(Event::class)->findAll();

        $this->assertCount(10, $events);

        $this->assertEquals(
            ['Event 10', 'Event 9', 'Event 8', 'Event 7', 'Event 6', 'Event 5', 'Event 4', 'Event 3', 'Event 2', 'Event 1'],
            array_map(fn (Event $event) => $event->getName(), $events)
        );
    }

    public function testFindByIdThrowsExceptionIfEventDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot find event with id: "cfe93415-d778-4115-bf3e-d45e9c497bc9"');

        $this->getRepository(Event::class)->findById('cfe93415-d778-4115-bf3e-d45e9c497bc9');
    }

    public function testFindById(): void
    {
        $this->loadFixture($fixture = new Events());

        $this->assertEquals(
            'Event 1',
            $this->getRepository(Event::class)->findById($fixture->event1Id)->getName()
        );
    }

    public function testSave(): void
    {
        $repo = $this->getRepository(Event::class);

        $this->assertEmpty($repo->findAll());

        $event = new Event(
            'Event 1',
            'Event 1',
            null,
            new \DateTime(),
            'Location 1',
            null
        );

        $repo->save($event);

        $events = $repo->findAll();
        $this->assertCount(1, $events);
        $this->assertEquals('Event 1', $events[0]->getName());
    }

    public function testRemove(): void
    {
        $this->loadFixture($fixture = new Events());

        $repo = $this->getRepository(Event::class);
        $event = $repo->findById($fixture->event1Id->toString());

        $repo->remove($event);

        $events = $repo->findAll();
        $this->assertCount(9, $events);

        $this->assertEquals(
            ['Event 10', 'Event 9', 'Event 8', 'Event 7', 'Event 6', 'Event 5', 'Event 4', 'Event 3', 'Event 2'],
            array_map(fn (Event $event) => $event->getName(), $events)
        );
    }
}
