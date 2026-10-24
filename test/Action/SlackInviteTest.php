<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Action;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\Website\Action\SlackInvite;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

class SlackInviteTest extends TestCase
{
    public function testSlackErrorsAreLoggedNotReturned(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('post')->willThrowException(new \RuntimeException('token=secret rejected'));
        $logger = new TestLogger();

        $request = (new ServerRequest('POST', '/api/slack-invite'))->withParsedBody(['email' => 'a@b.com']);
        $response = (new SlackInvite($client, 'secret', $logger))($request, new Response());

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('{"error":"An unknown error occurred"}', $response->getBody()->__toString());
        $this->assertTrue($logger->hasErrorThatContains('token=secret rejected'));
    }
}
