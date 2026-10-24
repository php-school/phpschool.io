<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Action\Admin\Event;

use GuzzleHttp\Psr7\Response;
use PhpSchool\Website\Action\Admin\Event\Delete;
use PhpSchool\Website\Repository\EventRepository;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteTest extends TestCase
{
    public function testUnknownEventReturnsError(): void
    {
        $repository = $this->createMock(EventRepository::class);

        $repository->expects($this->once())
            ->method('findById')
            ->with('non-existent-event')
            ->willThrowException(new \RuntimeException());

        $repository->expects($this->never())->method('remove');

        $action = new Delete($repository, $this->createMock(CacheItemPoolInterface::class));

        $actualResponse = $action->__invoke(
            $this->createMock(ServerRequestInterface::class),
            new Response(),
            'non-existent-event',
        );

        $this->assertEquals(404, $actualResponse->getStatusCode());
        $this->assertEquals(
            ['error' => 'Could not find event with id: non-existent-event'],
            json_decode($actualResponse->getBody()->__toString(), true),
        );
    }
}
