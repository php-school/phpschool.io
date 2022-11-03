<?php

namespace PhpSchool\WebsiteTest;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use InvalidArgumentException;
use PhpSchool\Website\PhpRenderer;
use PHPUnit\Framework\TestCase;
use Throwable;

class PhpRendererTest extends TestCase
{
    public function testRenderer(): void
    {
        $renderer = new PhpRenderer(__DIR__ . '/_files/');
        $body = new Stream(fopen('php://temp', 'r+'));
        $response = new Response(200, [], $body);
        $newResponse = $renderer->render($response, 'template.phtml', ['hello' => 'Hi']);
        $newResponse->getBody()->rewind();
        $this->assertEquals('Hi', $newResponse->getBody()->getContents());
    }

    public function testRenderConstructor(): void
    {
        $renderer = new PhpRenderer(__DIR__ . '/_files');
        $body = new Stream(fopen('php://temp', 'r+'));
        $response = new Response(200, [], $body);
        $newResponse = $renderer->render($response, 'template.phtml', ['hello' => 'Hi']);
        $newResponse->getBody()->rewind();
        $this->assertEquals('Hi', $newResponse->getBody()->getContents());
    }

    public function testAttributeMerging(): void
    {
        $renderer = new PhpRenderer(__DIR__ . '/_files/', [
            'hello' => 'Hello'
        ]);
        $body = new Stream(fopen('php://temp', 'r+'));
        $response = new Response(200, [], $body);
        $newResponse = $renderer->render($response, 'template.phtml', [
            'hello' => 'Hi'
        ]);
        $newResponse->getBody()->rewind();
        $this->assertEquals('Hi', $newResponse->getBody()->getContents());
    }

    public function testExceptionInTemplate(): void
    {
        $renderer = new PhpRenderer(__DIR__ . '/_files/');
        $body = new Stream(fopen('php://temp', 'r+'));
        $response = new Response(200, [], $body);
        try {
            $newResponse = $renderer->render($response, 'exception_layout.phtml');
        } catch (Throwable $t) {
            // Simulates an error template
            $newResponse = $renderer->render($response, 'template.phtml', [
                'hello' => 'Hi'
            ]);
        }

        $newResponse->getBody()->rewind();
        $this->assertEquals('Hi', $newResponse->getBody()->getContents());
    }

    public function testExceptionForTemplateInData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $renderer = new PhpRenderer(__DIR__ . '/_files/');
        $body = new Stream(fopen('php://temp', 'r+'));
        $response = new Response(200, [], $body);
        $renderer->render($response, 'template.phtml', [
            'template' => 'Hi'
        ]);
    }

    public function testTemplateNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $renderer = new PhpRenderer(__DIR__ . '/_files/');
        $body = new Stream(fopen('php://temp', 'r+'));
        $response = new Response(200, [], $body);
        $renderer->render($response, 'adfadftemplate.phtml', []);
    }

    public function testTemplateExists(): void
    {
        $renderer = new PhpRenderer(__DIR__ . '/_files/');
        $this->assertTrue($renderer->templateExists('layout.phtml'));
        $this->assertFalse($renderer->templateExists('non-existant-template'));
    }
}