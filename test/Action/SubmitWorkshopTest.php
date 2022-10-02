<?php

namespace PhpSchool\WebsiteTest\Action;

use GuzzleHttp\Psr7\Response;
use Laminas\InputFilter\InputFilter;
use PhpSchool\Website\Action\SubmitWorkshop;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\User\Session;
use PhpSchool\Website\Workshop\EmailNotifier;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;
use Psr\Log\Test\TestLogger;

class SubmitWorkshopTest extends TestCase
{
    public function testFormCanBeRendered(): void
    {
        $formHandler = new FormHandler(
            new InputFilter(),
            new Session()
        );

        $action = new SubmitWorkshop(
            $formHandler,
            $this->createMock(WorkshopCreator::class),
            $this->createMock(EmailNotifier::class),
            new NullLogger()
        );

        $renderer = $this->createMock(PhpRenderer::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);


        $renderer->expects($this->once())
            ->method('fetch')
            ->with('submit.phtml')
            ->willReturn('submit form');

        $renderer->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'layouts/layout.phtml',
                [
                    'pageTitle'       => 'Submit your workshop',
                    'pageDescription' => 'Submit your workshop to the workshop registry!',
                    'content'         => 'submit form'
                ]
            )
            ->willReturn($response);

        $actualResponse = $action->showSubmitForm($request, $response, $renderer);

        $this->assertSame($actualResponse, $response);
    }

    public function testSubmitReturnsErrorResponseIfFormInvalid(): void
    {
        $inputFilter = $this->createMock(InputFilter::class);
        $formHandler = new FormHandler(
            $inputFilter,
            new Session()
        );

        $action = new SubmitWorkshop(
            $formHandler,
            $this->createMock(WorkshopCreator::class),
            $this->createMock(EmailNotifier::class),
            new NullLogger()
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        $inputFilter->expects($this->once())
            ->method('setData')
            ->with([]);

        $inputFilter->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $inputFilter->expects($this->once())
            ->method('getMessages')
            ->willReturn(['field1' => ['error1'], 'field2' => ['error2']]);

        $actualResponse = $action->submit($request, $response);

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => false,
                'form_errors' => [
                    'field1' => ['error1'],
                    'field2' => ['error2'],
                ]
            ],
            json_decode($actualResponse->getBody(), true)
        );
    }

    public function testSubmitReturnsErrorResponseIfWorkshopCannotBeCreated(): void
    {
        $inputFilter = $this->createMock(InputFilter::class);
        $formHandler = new FormHandler(
            $inputFilter,
            new Session()
        );
        $workshopCreator = $this->createMock(WorkshopCreator::class);

        $action = new SubmitWorkshop(
            $formHandler,
            $workshopCreator,
            $this->createMock(EmailNotifier::class),
            new NullLogger()
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        $inputFilter->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $inputFilter->expects($this->once())
            ->method('getValues')
            ->willReturn([]);

        $workshopCreator
            ->expects($this->once())
            ->method('create')
            ->with([])
            ->willThrowException(new WorkshopCreationException([
                'field1' => ['error1'],
                'field2' => ['error2'],
            ]));

        $actualResponse = $action->submit($request, $response);

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => false,
                'workshop_errors' => [
                    'field1' => ['error1'],
                    'field2' => ['error2'],
                ]
            ],
            json_decode($actualResponse->getBody(), true)
        );
    }

    public function testErrorIsLoggedIfEmailNotificationEmailNotSent(): void
    {
        $logger = new TestLogger();
        $inputFilter = $this->createMock(InputFilter::class);
        $formHandler = new FormHandler(
            $inputFilter,
            new Session()
        );
        $workshopCreator = $this->createMock(WorkshopCreator::class);
        $emailer = $this->createMock(EmailNotifier::class);

        $action = new SubmitWorkshop(
            $formHandler,
            $workshopCreator,
            $emailer,
            $logger
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        $inputFilter->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $inputFilter->expects($this->once())
            ->method('getValues')
            ->willReturn([]);

        $workshopCreator
            ->expects($this->once())
            ->method('create')
            ->with([])
            ->willReturn($this->createMock(Workshop::class));

        $emailer
            ->expects($this->once())
            ->method('new')
            ->willThrowException(new \RuntimeException('Could not send'));

        $actualResponse = $action->submit($request, $response);

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(['success' => true], json_decode($actualResponse->getBody(), true));
        $this->assertTrue($logger->hasErrorThatContains('Email could not be sent. Error: "Could not send"'));
    }

    public function testSuccessfulCreation(): void
    {
        $logger = new TestLogger();
        $inputFilter = $this->createMock(InputFilter::class);
        $formHandler = new FormHandler(
            $inputFilter,
            new Session()
        );
        $workshopCreator = $this->createMock(WorkshopCreator::class);

        $action = new SubmitWorkshop(
            $formHandler,
            $workshopCreator,
            $this->createMock(EmailNotifier::class),
            $logger
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        $inputFilter->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $inputFilter->expects($this->once())
            ->method('getValues')
            ->willReturn([]);

        $workshopCreator
            ->expects($this->once())
            ->method('create')
            ->with([])
            ->willReturn($this->createMock(Workshop::class));

        $actualResponse = $action->submit($request, $response);

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(['success' => true], json_decode($actualResponse->getBody(), true));
        $this->assertEmpty($logger->records);
    }
}
