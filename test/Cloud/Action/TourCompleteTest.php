<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Cloud\Action;

use GuzzleHttp\Psr7\Response;
use PhpSchool\Website\Action\Online\TourComplete;
use PhpSchool\Website\Online\StudentCloudState;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PhpSchool\Website\User\StudentRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

class TourCompleteTest extends TestCase
{
    public function testExceptionIsThrownIfStudentNotLoggedIn(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Needs a logged in user');

        $session = $this->createMock(SessionStorageInterface::class);
        $session->expects($this->once())
            ->method('get')
            ->with('student')
            ->willReturn(false);

        $repo = $this->createMock(StudentRepository::class);

        $action = new TourComplete($session, $repo);

        $action->__invoke($this->createMock(ServerRequestInterface::class), new Response());
    }

    public function testStudentIsUpdated(): void
    {
        $student = new StudentDTO(
            $id = Uuid::uuid4(),
            'Student 1',
            'student@phpschool.com',
            'Student 1',
            null,
            null,
            new \DateTime('14 February 2022'),
            false,
            new StudentCloudState([])
        );

        $studentEntity = $this->createMock(Student::class);
        $studentEntity->expects($this->once())
            ->method('setTourComplete');

        $session = $this->createMock(SessionStorageInterface::class);
        $session->expects($this->once())
            ->method('get')
            ->with('student')
            ->willReturn($student);

        $repo = $this->createMock(StudentRepository::class);

        $repo->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($studentEntity);

        $repo->expects($this->once())
            ->method('update')
            ->with($studentEntity);

        $action = new TourComplete($session, $repo);

        $response = $action->__invoke($this->createMock(ServerRequestInterface::class), new Response());

        $this->assertEquals(['success' => true], json_decode($response->getBody()->__toString(), true));
    }
}
