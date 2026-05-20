<?php

declare(strict_types=1);

use DjinnDev\Psr17\RequestFactory;
use DjinnDev\Psr17\UriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class RequestFactoryTest extends TestCase
{
    public function testCreateRequestMethod(): void
    {
        $method = 'GET';
        $uri = 'http://tgeene.me';
        $this->assertInstanceOf(RequestInterface::class, RequestFactory::getInstance()->createRequest($method, $uri));

        $uri = UriFactory::getInstance()->createUri($uri);
        $this->assertInstanceOf(RequestInterface::class, RequestFactory::getInstance()->createRequest($method, $uri));
    }
}
