<?php

namespace PhpSchool\WebsiteTest\Cloud;

use Composer\InstalledVersions;
use PhpSchool\Website\Online\CloudInstalledWorkshop;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Repository\WorkshopRepository;
use PHPUnit\Framework\TestCase;

class CloudWorkshopRepositoryTest extends TestCase
{
    public function testFindByCodeThrowsExceptionWhenWorkshopIsNotInstalledViaComposer(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot find workshop with code: "not-installed-workshop"');

        $workshopRepo = $this->createMock(WorkshopRepository::class);
        $repo = new CloudWorkshopRepository($workshopRepo);

        $repo->findByCode('not-installed-workshop');
    }

    public function testFindAll(): void
    {
        $workshopRepo = $this->createMock(WorkshopRepository::class);
        $repo = new CloudWorkshopRepository($workshopRepo);

        $workshops = $repo->findAll();

        $this->assertCount(2, $workshops);
    }

    public function testFindByCode(): void
    {
        $php8appreciate = $this->createMock(Workshop::class);
        $php8appreciate->expects($this->atLeastOnce())->method('getCode')->willReturn('php8appreciate');

        $learnYouPhp = $this->createMock(Workshop::class);
        $learnYouPhp->expects($this->atLeastOnce())->method('getCode')->willReturn('learnyouphp');

        $workshopRepo = $this->createMock(WorkshopRepository::class);
        $workshopRepo->expects($this->atLeastOnce())
            ->method('findByCode')
            ->willReturnMap([
                ['learnyouphp', $learnYouPhp],
                ['php8appreciate', $php8appreciate],
            ]);

        $repo = new CloudWorkshopRepository($workshopRepo);

        $workshop = $repo->findByCode('php8appreciate');

        $this->assertInstanceOf(CloudInstalledWorkshop::class, $workshop);
    }

    public function testTotalExerciseCount(): void
    {
        $workshopRepo = $this->createMock(WorkshopRepository::class);
        $repo = new CloudWorkshopRepository($workshopRepo);

        $this->assertEquals(24, $repo->totalExerciseCount());
    }
}
