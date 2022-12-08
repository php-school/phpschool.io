<?php

namespace PhpSchool\Website\Cloud\Command;

use GuzzleHttp\Exception\TransferException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadComposerPackageList
{
    private const COMPOSER_PACKAGES_API_URL = 'https://packagist.org/packages/list.json';
    private const COMPOSER_PACKAGES_FILE_LOCATION = __DIR__ . '/../../../var/packages.json';
    private const COMPOSER_PACKAGES_NEW_FILE_LOCATION = __DIR__ . '/../../../var/packages-new.json';

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(OutputInterface $output): void
    {
        try {
            $response = (new \GuzzleHttp\Client())
                ->request(
                    'GET',
                    self::COMPOSER_PACKAGES_API_URL,
                    ['sink' => self::COMPOSER_PACKAGES_NEW_FILE_LOCATION]
                );
        } catch (TransferException $e) {
            $this->logger->error('Could not download composer packages. Error: ' . $e->getMessage());
            return;
        }

        if ($response->getStatusCode() !== 200) {
            $this->logger->error('Could not download composer packages. Status Code: ' . $response->getStatusCode());
            return;
        }

        copy(self::COMPOSER_PACKAGES_NEW_FILE_LOCATION, self::COMPOSER_PACKAGES_FILE_LOCATION);
        unlink(self::COMPOSER_PACKAGES_NEW_FILE_LOCATION);

        $output->writeln('<info>Composer packages downloaded</info>');
    }
}
