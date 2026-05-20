<?php

declare(strict_types=1);

use DjinnDev\Psr17\ServerRequestFactory;
use DjinnDev\Psr17\UriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class ServerRequestFactoryTest extends TestCase
{
    public function testCreateServerRequestMethod(): void
    {
        $method = 'GET';
        $uri = 'http://tgeene.me';
        $serverParams = ['foo' => 'bar'];

        $this->assertInstanceOf(ServerRequestInterface::class, ServerRequestFactory::getInstance()->createServerRequest($method, $uri, $serverParams));

        $uri = UriFactory::getInstance()->createUri($uri);
        $this->assertInstanceOf(ServerRequestInterface::class, ServerRequestFactory::getInstance()->createServerRequest($method, $uri, $serverParams));
    }
}
