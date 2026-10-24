<?php

declare(strict_types=1);

namespace PhpSchool\Website\Middleware;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Symfony\Component\RateLimiter\RateLimiterFactory;

/**
 * Limits a public route per visitor IP, eg 5 admin login attempts a minute.
 * Needs the IpAddress middleware to run first, it sets the "ip_address" attribute.
 */
class IpRateLimiter
{
    public function __construct(private readonly RateLimiterFactory $limiterFactory) {}

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        /** @var string $ip */
        $ip = $request->getAttribute('ip_address');
        $limit = $this->limiterFactory->create($ip)->consume();

        if (!$limit->isAccepted()) {
            return new GuzzleResponse(
                429,
                ['Content-Type' => 'application/json'],
                (string) json_encode(['error' => 'Too many requests, please try again later.']),
            );
        }

        return $handler->handle($request);
    }
}
