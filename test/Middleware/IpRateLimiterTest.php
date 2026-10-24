<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\Website\Middleware\IpRateLimiter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

class IpRateLimiterTest extends TestCase
{
    public function testRequestsOverTheLimitAreRejectedPerIp(): void
    {
        $limiter = new IpRateLimiter(new RateLimiterFactory(
            ['id' => 'test', 'policy' => 'sliding_window', 'limit' => 2, 'interval' => '1 minute'],
            new InMemoryStorage(),
        ));

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };

        $request = fn(string $ip) => (new ServerRequest('POST', '/api/admin/login'))->withAttribute('ip_address', $ip);

        $this->assertSame(200, $limiter($request('1.1.1.1'), $handler)->getStatusCode());
        $this->assertSame(200, $limiter($request('1.1.1.1'), $handler)->getStatusCode());
        $this->assertSame(429, $limiter($request('1.1.1.1'), $handler)->getStatusCode());
        $this->assertSame(200, $limiter($request('2.2.2.2'), $handler)->getStatusCode());
    }
}
