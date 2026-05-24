<?php

declare(strict_types=1);

use DjinnDev\Psr17\RequestFactory;
use DjinnDev\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class RequestFactoryTest extends TestCase
{
    public function testCreateRequestIsRequestInterface(): void
    {
        $this->assertInstanceOf(RequestInterface::class, RequestFactory::getInstance()->createRequest('GET', 'https://tgeene.me/'));
    }

    public function testCreateRequestParameters(): void
    {
        $uri = 'https://tgeene.me/';
        foreach (Request::VALID_REQUEST_METHODS as $method => $true)
        {
            $request = RequestFactory::getInstance()->createRequest($method, $uri);
            $this->assertEquals($method, $request->getMethod());
            $this->assertInstanceOf(UriInterface::class, $request->getUri());
            $this->assertEquals($uri, (string) $request->getUri());
        }
    }
}
