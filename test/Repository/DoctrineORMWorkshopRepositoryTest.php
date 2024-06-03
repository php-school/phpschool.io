<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Repository;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\WebsiteTest\Repository\Fixtures\ApprovedAndUnApprovedWorkshops;

class DoctrineORMWorkshopRepositoryTest extends DoctrineORMRepositoryTest
{
    public function testFindAllPendingApproval(): void
    {
        $this->loadFixture(new ApprovedAndUnApprovedWorkshops());

        $workshops = $this->getRepository(Workshop::class)->findAllPendingApproval();

        $this->assertCount(1, $workshops);
        $this->assertEquals('learnyouphp', $workshops[0]->getCode());
    }

    public function testFindAllApproved(): void
    {
        $this->loadFixture(new ApprovedAndUnApprovedWorkshops());

        $workshops = $this->getRepository(Workshop::class)->findAllApproved();

        $this->assertCount(1, $workshops);
        $this->assertEquals('php8appreciate', $workshops[0]->getCode());
    }

    public function testFindAll(): void
    {
        $this->loadFixture(new ApprovedAndUnApprovedWorkshops());

        $workshops = $this->getRepository(Workshop::class)->findAll();

        $this->assertCount(2, $workshops);
        $this->assertEquals('php8appreciate', $workshops[0]->getCode());
        $this->assertEquals('learnyouphp', $workshops[1]->getCode());
    }

    public function testFindByIdThrowsExceptionIfWorkshopDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot find workshop with id: "cfe93415-d778-4115-bf3e-d45e9c497bc9"');

        $this->getRepository(Workshop::class)->findById('cfe93415-d778-4115-bf3e-d45e9c497bc9');
    }

    public function testFindById(): void
    {
        $this->loadFixture($fixture = new ApprovedAndUnApprovedWorkshops());

        $this->assertEquals(
            'learnyouphp',
            $this->getRepository(Workshop::class)->findById($fixture->learnYouPhpId->toString())->getCode()
        );
    }

    public function testFindByCodeThrowsExceptionIfWorkshopDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot find workshop with code: "not-a-workshop"');

        $this->getRepository(Workshop::class)->findByCode('not-a-workshop');
    }

    public function testFindByCode(): void
    {
        $this->loadFixture(new ApprovedAndUnApprovedWorkshops());

        $this->assertEquals('learnyouphp', $this->getRepository(Workshop::class)->findByCode('learnyouphp')->getCode());
    }

    public function testFindByDisplayNameThrowsExceptionIfWorkshopDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot find workshop with display name: "Not A Workshop"');

        $this->getRepository(Workshop::class)->findByDisplayName('Not A Workshop');
    }

    public function testFindByDisplayName(): void
    {
        $this->loadFixture(new ApprovedAndUnApprovedWorkshops());

        $this->assertEquals('learnyouphp', $this->getRepository(Workshop::class)->findByDisplayName('Learn You PHP')->getCode());
    }

    public function testSave(): void
    {
        $repo = $this->getRepository(Workshop::class);

        $this->assertEmpty($repo->findAll());

        $workshop = new Workshop(
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

        $repo->save($workshop);

        $workshops = $repo->findAll();
        $this->assertCount(1, $workshops);
        $this->assertEquals('php8appreciate', $workshops[0]->getCode());
    }

    public function testRemove(): void
    {
        $this->loadFixture($fixture = new ApprovedAndUnApprovedWorkshops());

        $repo = $this->getRepository(Workshop::class);
        $workshop = $repo->findById($fixture->learnYouPhpId->toString());

        $repo->remove($workshop);

        $workshops = $repo->findAll();
        $this->assertCount(1, $workshops);
        $this->assertEquals('php8appreciate', $workshops[0]->getCode());
    }
}
