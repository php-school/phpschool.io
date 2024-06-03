<?php

declare(strict_types=1);

namespace PhpSchool\Website\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncContributors
{
    private const CONTRIBUTORS_FILE_LOCATION = __DIR__ . '/../../var/contributors.json';

    public function __construct(private \Github\Client $client, private LoggerInterface $logger) {}

    public function __invoke(OutputInterface $output): void
    {
        try {
            $contributors = $this->downloadContributors();
        } catch (\Exception $e) {
            $this->logger->error('Could not download contributors. Error: ' . $e->getMessage());
            return;
        }

        file_put_contents(self::CONTRIBUTORS_FILE_LOCATION, json_encode($contributors, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        $output->writeln('<info>Contributors synced</info>');
    }

    /**
     * @return array<int, array{
     *     username: string,
     *     contributions: int,
     *     profilePic: string,
     *     profile: string
     * }>
     */
    private function downloadContributors(): array
    {
        $repositories = $this->client->user()->repositories('php-school');

        $contributors = [];

        foreach ($repositories as $repository) {
            $result = $this->client->repositories()->contributors('php-school', $repository['name']);

            if (!is_array($result)) {
                continue;
            }

            foreach ($result as $contributor) {
                if (!isset($contributors[$contributor['login']])) {
                    $contributors[$contributor['login']] = [
                        'username' => $contributor['login'],
                        'contributions' => (int) $contributor['contributions'],
                        'profilePic' => $contributor['avatar_url'],
                        'profile' => $contributor['html_url'],
                    ];
                } else {
                    $contributors[$contributor['login']]['contributions'] += (int) $contributor['contributions'];
                }
            }
        }

        usort($contributors, fn(array $a, array $b) => $b['contributions'] <=> $a['contributions']);

        return $contributors;
    }
}
