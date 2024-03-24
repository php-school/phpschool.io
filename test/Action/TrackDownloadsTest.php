<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Action;

use GuzzleHttp\Psr7\Response;
use PhpSchool\Website\Action\TrackDownloads;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class TrackDownloadsTest extends TestCase
{
    public function testUnknownWorkshopReturnsError(): void
    {
        $workshopRepo = $this->createMock(WorkshopRepository::class);
        $workshopInstallRepo = $this->createMock(WorkshopInstallRepository::class);

        $action = new TrackDownloads($workshopRepo, $workshopInstallRepo);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        $workshopRepo->expects($this->once())
            ->method('findByCode')
            ->with('non-existent-workshop')
            ->willThrowException(new \RuntimeException());

        $actualResponse = $action->__invoke(
            $request,
            $response,
            'non-existent-workshop',
            '9.9.9'
        );

        $this->assertEquals(404, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'status' => 'error',
                'message' => 'Workshop: "non-existent-workshop" not found.'
            ],
            json_decode($actualResponse->getBody()->__toString(), true)
        );
    }

    public function testNewWorkshopInstallIsCreated(): void
    {
        $workshopRepo = $this->createMock(WorkshopRepository::class);
        $workshopInstallRepo = $this->createMock(WorkshopInstallRepository::class);

        $action = new TrackDownloads($workshopRepo, $workshopInstallRepo);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        $request->expects($this->once())
            ->method('getAttribute')
            ->with('ip_address')
            ->willReturn('127.0.0.1');

        $workshop = $this->createMock(Workshop::class);

        $workshopRepo->expects($this->once())
            ->method('findByCode')
            ->with('my-workshop')
            ->willReturn($workshop);

        $workshopInstallRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (WorkshopInstall $install) use ($workshop) {
                $this->assertEquals($install->getWorkshop(), $workshop);
                $this->assertEquals($install->getIpAddress(), '127.0.0.1');
                $this->assertEquals($install->getVersion(), '1.0.0');
                return true;
            }));

        $actualResponse = $action->__invoke(
            $request,
            $response,
            'my-workshop',
            '1.0.0'
        );

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(['success' => true], json_decode($actualResponse->getBody()->__toString(), true));
    }
}
