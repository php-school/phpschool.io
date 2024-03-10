<?php

namespace PhpSchool\Website\Online\Middleware;

use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class ExerciseRunnerRateLimiter
{
    private SessionStorageInterface $session;
    private RateLimiterFactory $limiterFactory;

    public function __construct(SessionStorageInterface $session, RateLimiterFactory $limiterFactory)
    {
        $this->session = $session;
        $this->limiterFactory = $limiterFactory;
    }

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
                $headers
            );
        }

        $response = $handler->handle($request);

        foreach ($headers as $header => $val) {
            $response = $response->withHeader($header, $val);
        }

        return $response;
    }
}
