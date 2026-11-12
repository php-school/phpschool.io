<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use Predis\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Runs a verify or run on the docker host, waiting for docker while Fly boots the machine from scratch. fly/start.sh
 * labels dockerd with the time it started, so a cold start is known from the label, not guessed from the timing, and
 * every other run can be held to being fast.
 */
class Sandbox
{
    private const WAIT_SECONDS = 30;

    public function __construct(
        private readonly ClientInterface $redis,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @template T
     * @param callable(): T $run
     * @return T
     */
    public function run(callable $run): mixed
    {
        $start = microtime(true);
        $dockerStarted = $this->waitForDocker();
        $cold = $dockerStarted !== null && $dockerStarted >= (int) $start;

        try {
            return $run();
        } finally {
            $seconds = round(microtime(true) - $start, 1);
            $this->logger->info(sprintf('Sandbox run took %ss (%s)', $seconds, $cold ? 'cold start' : 'warm'));

            if ($cold) {
                $this->countColdStart($dockerStarted);
            } elseif ($seconds > 30) {
                $this->logger->error(sprintf('A sandbox run took %ss without a cold start', $seconds));
            }
        }
    }

    /**
     * The unix time dockerd started, or null when it has no label, eg. local docker
     */
    private function waitForDocker(): ?int
    {
        $docker = (new ExecutableFinder())->find('docker') ?? throw new \RuntimeException('docker is not installed');
        $deadline = time() + self::WAIT_SECONDS;

        while (true) {
            $process = new Process([$docker, 'info', '--format', '{{range .Labels}}{{println .}}{{end}}'], timeout: self::WAIT_SECONDS);
            $process->run();

            if ($process->isSuccessful()) {
                return preg_match('/^started=(\d+)$/m', $process->getOutput(), $matches) ? (int) $matches[1] : null;
            }

            if (time() >= $deadline) {
                throw new \RuntimeException(sprintf('The sandbox did not start within %ds: %s', self::WAIT_SECONDS, trim($process->getErrorOutput())));
            }

            sleep(1);
        }
    }

    private function countColdStart(int $dockerStarted): void
    {
        //runs that waited on the same boot count once
        $key = 'sandbox-cold-starts:' . date('Y-m-d');
        $this->redis->sadd($key, [(string) $dockerStarted]);
        $this->redis->expire($key, 2 * 86400);

        $count = $this->redis->scard($key);
        if ($count > 3) {
            $this->logger->error(sprintf('The sandbox cold started %d times today', $count));
        }
    }
}
