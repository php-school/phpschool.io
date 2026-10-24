<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Action\Admin\Event;

use GuzzleHttp\Psr7\Response;
use PhpSchool\Website\Action\Admin\Event\Update;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\Repository\EventRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class UpdateTest extends TestCase
{
    public function testUnknownEventReturnsError(): void
    {
        $repository = $this->createMock(EventRepository::class);
        $formHandler = $this->createMock(FormHandler::class);

        $repository->expects($this->once())
            ->method('findById')
            ->with('non-existent-event')
            ->willThrowException(new \RuntimeException());

        $formHandler->expects($this->never())->method('validateJsonRequest');

        $action = new Update($repository, $formHandler);

        $actualResponse = $action->__invoke(
            $this->createMock(ServerRequestInterface::class),
            new Response(),
            'non-existent-event',
        );

        $this->assertEquals(404, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            ['error' => 'Could not find event with id: non-existent-event'],
            json_decode($actualResponse->getBody()->__toString(), true),
        );
    }
}
