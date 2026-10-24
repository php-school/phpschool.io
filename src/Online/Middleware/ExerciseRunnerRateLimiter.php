<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online\Middleware;

use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Semaphore\SemaphoreFactory;

class ExerciseRunnerRateLimiter
{
    public function __construct(
        private readonly SessionStorageInterface $session,
        private readonly RateLimiterFactory $limiterFactory,
        private readonly SemaphoreFactory $semaphoreFactory,
        //how many sandbox containers can run at once, across every student
        private readonly int $maxRunning = 4,
        //how long a request waits for a free slot before giving up
        private readonly float $maxWaitSeconds = 20,
    ) {}

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        /** @var StudentDTO $student */
        $student = $this->session->get('student');

        $limiter = $this->limiterFactory->create($student->id->toString());

        $limit = $limiter->consume();

        $headers = [
            'X-RateLimit-Remaining' => (string) $limit->getRemainingTokens(),
            'X-RateLimit-Retry-After' => (string) $limit->getRetryAfter()->getTimestamp(),
            'X-RateLimit-Limit' => (string) $limit->getLimit(),
        ];

        if (false === $limit->isAccepted()) {
            return new \GuzzleHttp\Psr7\Response(
                429,
                $headers,
            );
        }

        //a run can take a few container timeouts (30s each), the ttl frees the slot if php dies mid run
        $slot = $this->semaphoreFactory->createSemaphore('exerciseRunner', $this->maxRunning, ttlInSecond: 120);

        $giveUpAt = microtime(true) + $this->maxWaitSeconds;
        while (!$slot->acquire()) {
            if (microtime(true) >= $giveUpAt) {
                return new \GuzzleHttp\Psr7\Response(503, ['Retry-After' => '10']);
            }
            usleep(250_000);
        }

        try {
            $response = $handler->handle($request);
        } finally {
            $slot->release();
        }

        foreach ($headers as $header => $val) {
            $response = $response->withHeader($header, $val);
        }

        return $response;
    }
}
