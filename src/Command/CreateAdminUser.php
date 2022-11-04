<?php

namespace PhpSchool\Website\Command;

use Doctrine\ORM\EntityManagerInterface;
use PhpSchool\Website\Cache;
use PhpSchool\Website\User\Entity\Admin;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminUser
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(OutputInterface $output, string $name, string $email, string $password): int
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $this->entityManager->persist(
            new Admin($name, $email, $password)
        );
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $output->writeln(
                sprintf("\n<error>User %s was not created. Error: %s!</error>\n", $email, $e->getMessage())
            );
            return 1;
        }
        $output->writeln(sprintf("\n<info>User %s was created!</info>\n", $email));
        return 0;
    }
}
