<?php

declare(strict_types=1);

use DjinnDev\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ResponseTest extends TestCase
{
    public function testConstruct(): void
    {
        $request = new Response();
        $this->assertInstanceOf(ResponseInterface::class, $request);
    }

    public function testStatusMethods(): void
    {
        foreach (Response::STATUS_CODE_REASONS as $statusCode => $reasonPhrase)
        {
            $response = new Response();
            $this->assertInstanceOf(ResponseInterface::class, $response->withStatus($statusCode));

            $response = new Response($statusCode, $reasonPhrase);
            $this->assertEquals($statusCode, $response->getStatusCode());
            $this->assertEquals($reasonPhrase, $response->getReasonPhrase());

            $response = new Response();
            $response = $response->withStatus($statusCode);
            $this->assertEquals($statusCode, $response->getStatusCode());
            $this->assertEquals($reasonPhrase, $response->getReasonPhrase());

            $response = new Response($statusCode, $reasonPhrase.$statusCode);
            $this->assertNotEquals($reasonPhrase, $response->getReasonPhrase());

            $response = new Response();
            $response = $response->withStatus($statusCode, $reasonPhrase.$statusCode);
            $this->assertNotEquals($reasonPhrase, $response->getReasonPhrase());
        }
    }
}
