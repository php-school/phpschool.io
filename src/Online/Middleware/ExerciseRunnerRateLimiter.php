<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online\Middleware;

use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class ExerciseRunnerRateLimiter
{
    public function __construct(
        private readonly SessionStorageInterface $session,
        private readonly RateLimiterFactory $limiterFactory,
        //shared by every student, so lots of accounts can't start more sandbox containers than the server can run
        private readonly RateLimiterFactory $allStudentsLimiterFactory,
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

        if (false === $limit->isAccepted() || false === $this->allStudentsLimiterFactory->create('all')->consume()->isAccepted()) {
            return new \GuzzleHttp\Psr7\Response(
                429,
                $headers,
            );
        }

        $response = $handler->handle($request);

        foreach ($headers as $header => $val) {
            $response = $response->withHeader($header, $val);
        }

        return $response;
    }
}
