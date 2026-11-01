<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\Website\ErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use Slim\CallableResolver;
use Slim\Exception\HttpNotFoundException;

class ErrorHandlerTest extends TestCase
{
    public function testNotFoundIsNotLogged(): void
    {
        $logger = new TestLogger();
        $request = new ServerRequest('GET', '/wp-admin');

        $response = $this->handler($logger)($request, new HttpNotFoundException($request), false, true, true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertFalse($logger->hasErrorRecords());
    }

    public function testServerErrorsAreLogged(): void
    {
        $logger = new TestLogger();

        $response = $this->handler($logger)(new ServerRequest('GET', '/api/posts'), new \RuntimeException('Boom'), false, true, true);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertTrue($logger->hasErrorThatContains('Boom'));
    }

    private function handler(TestLogger $logger): ErrorHandler
    {
        return new ErrorHandler(new CallableResolver(), new HttpFactory(), $logger);
    }
}
