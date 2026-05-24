<?php

declare(strict_types=1);

use DjinnDev\Psr17\UriFactory;
use DjinnDev\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class RequestTest extends TestCase
{
    public function testConstruct(): void
    {
        $request = new Request();
        $this->assertInstanceOf(RequestInterface::class, $request);
    }

    public function testRequestTargetMethods(): void
    {
        $uri = 'https://tgeene.me/';

        $request = new Request();
        $request = $request->withRequestTarget($uri);
        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals($request, $request->withRequestTarget($uri));
        $this->assertEquals($uri, $request->getRequestTarget());
        $this->assertNotEquals($request, $request->withRequestTarget($uri . 'index.php'));
    }

    public function testMethodMethods(): void
    {
        foreach (Request::VALID_REQUEST_METHODS as $method => $true)
        {
            $request = new Request();

            $request = $request->withMethod($method);
            $this->assertInstanceOf(RequestInterface::class, $request);
            $this->assertEquals($request, $request->withMethod($method));
            $this->assertEquals($method, $request->getMethod());

            $method = strtolower($method);
            $request = $request->withMethod($method);
            $this->assertNotEquals($method, $request->getMethod());

        }

        $this->expectException(InvalidArgumentException::class);
        $request = new Request();
        $request->withMethod(implode('|', Request::VALID_REQUEST_METHODS));
    }

    public function testUriMethods(): void
    {
        $uri = UriFactory::getInstance()->createUri('https://tgeene.me/');
        $uriWithPort = $uri->withPort(8080);
        $uriAlternative = UriFactory::getInstance()->createUri('https://djinn.dev/');

        $request = new Request();

        $request = $request->withUri($uri);
        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals($request, $request->withUri($uri));
        $this->assertEquals($uri, $request->getUri());

        $request = $request->withUri($uri);
        $this->assertNotEquals($request, $request->withUri($uriWithPort));

        $request = $request->withUri($uri);
        $this->assertNotEquals($request, $request->withUri($uriAlternative));
    }
}
