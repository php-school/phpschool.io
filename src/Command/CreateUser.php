<?php

namespace PhpSchool\Website\Command;

use Doctrine\ORM\EntityManagerInterface;
use PhpSchool\Website\Cache;
use PhpSchool\Website\User\Entity\User;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class CreateUser
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(OutputInterface $output, string $name, string $email, string $password)
    {
        $this->entityManager->persist(
            new User($name, $email, password_hash($password, PASSWORD_DEFAULT))
        );
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $output->writeln(sprintf("\n<error>User %s was not created. Error: %s!</error>\n", $email, $e->getMessage()));
            return 1;
        }
        $output->writeln(sprintf("\n<info>User %s was created!</info>\n", $email));
    }
}
