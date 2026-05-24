<?php

declare(strict_types=1);

use DjinnDev\Psr17\ServerRequestFactory;
use DjinnDev\Psr17\UriFactory;
use DjinnDev\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

final class ServerRequestFactoryTest extends TestCase
{
    public function testCreateServerRequestIsServerRequestInterface(): void
    {
        $this->assertInstanceOf(ServerRequestInterface::class, ServerRequestFactory::getInstance()->createServerRequest('GET', 'https://tgeene.me/'));
    }

    public function testCreateRequestParameters(): void
    {
        $uri = 'https://tgeene.me/';
        $serverParams = ['foo' => 'bar'];
        foreach (Request::VALID_REQUEST_METHODS as $method => $true)
        {
            $serverRequest = ServerRequestFactory::getInstance()->createServerRequest($method, $uri, $serverParams);
            $this->assertEquals($method, $serverRequest->getMethod());
            $this->assertInstanceOf(UriInterface::class, $serverRequest->getUri());
            $this->assertEquals($uri, (string) $serverRequest->getUri());
            $this->assertEquals($serverParams, $serverRequest->getServerParams());
        }
    }
}
