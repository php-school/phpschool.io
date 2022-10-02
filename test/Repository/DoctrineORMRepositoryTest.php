<?php

namespace PhpSchool\WebsiteTest\Repository;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidType;
use Doctrine\Common\DataFixtures\Loader;

abstract class DoctrineORMRepositoryTest extends TestCase
{
    private SchemaTool $schemaTool;
    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        if (!Type::hasType('uuid')) {
            Type::addType('uuid', UuidType::class);
        }

        $config = ORMSetup::createAnnotationMetadataConfiguration(
            [
                'src/Entity',
                'src/User/Entity',
            ],
            true,
            sys_get_temp_dir(),
        );

        $this->entityManager = EntityManager::create(
            ['driver' => 'pdo_sqlite', 'url' => 'sqlite:///:memory:'],
            $config
        );

        $this->schemaTool = new SchemaTool($this->entityManager);
        $this->schemaTool->updateSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
    }

    public function loadFixture(FixtureInterface ...$fixtures): void
    {
        $loader = new Loader();

        foreach ($fixtures as $fixture) {
            $loader->addFixture($fixture);
        }

        $executor = new ORMExecutor($this->entityManager, new ORMPurger());
        $executor->execute($loader->getFixtures());
    }

    public function tearDown(): void
    {
        $this->schemaTool->dropSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
    }

    public function getRepository(string $class): EntityRepository
    {
        return $this->entityManager->getRepository($class);
    }
}
