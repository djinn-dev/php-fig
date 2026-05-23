<?php

declare(strict_types=1);

use DjinnDev\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

final class UriTest extends TestCase
{
    public function testConstruct(): void
    {
        $uri = new Uri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
        $this->assertEquals('', (string) $uri);
    }

    public function testSchemeMethods(): void
    {
        $scheme = 'http';

        $uri = new Uri(scheme: $scheme);
        $this->assertEquals($uri, $uri->withScheme($scheme));
        $scheme .= 's';
        $this->assertInstanceOf(UriInterface::class, $uri->withScheme($scheme));
        $uri = $uri->withScheme($scheme);
        $this->assertEquals($scheme, $uri->getScheme());
        $this->assertEquals($scheme . '://', (string) $uri);
    }

    public function testUserInfoMethods(): void
    {
        $user = 'user';

        $uri = new Uri(user: $user);
        $this->assertEquals($uri, $uri->withUserInfo($user));
        $user .= 'name';
        $this->assertInstanceOf(UriInterface::class, $uri->withUserInfo($user));
        $uri = $uri->withUserInfo($user);
        $this->assertEquals($user, $uri->getUserInfo());
        $this->assertEquals($user . '@', (string) $uri);

        $user = 'user';
        $password = 'pass';

        $uri = new Uri(user: $user, password: $password);
        $this->assertEquals($uri, $uri->withUserInfo($user, $password));
        $user .= 'name';
        $password .= 'word';
        $this->assertInstanceOf(UriInterface::class, $uri->withUserInfo($user, $password));
        $uri = $uri->withUserInfo($user, $password);
        $userInfo = $user . ':' . $password;
        $this->assertEquals($userInfo, $uri->getUserInfo());
        $this->assertEquals($userInfo . '@', (string) $uri);
    }

    public function testHostMethods(): void
    {
        $host = 'djinn.dev';

        $uri = new Uri(host: $host);
        $this->assertEquals($uri, $uri->withHost($host));
        $host = 'www.' . $host;
        $this->assertInstanceOf(UriInterface::class, $uri->withHost($host));
        $uri = $uri->withHost($host);
        $this->assertEquals($host, $uri->getHost());
        $this->assertEquals($host, (string) $uri);
    }

    public function testPortMethods(): void
    {
        $port = 8080;

        $uri = new Uri(port: $port);
        $this->assertEquals($uri, $uri->withPort($port));
        $port = 8181;
        $this->assertInstanceOf(UriInterface::class, $uri->withPort($port));
        $uri = $uri->withPort($port);
        $this->assertEquals($port, $uri->getPort());
        $this->assertEquals(':' . $port, (string) $uri);
        $uri = $uri->withPort(null);
        $this->assertEquals('', (string) $uri);
    }

    public function testPathMethods(): void
    {
        $path = 'path';

        $uri = new Uri(path: $path);
        $this->assertEquals($uri, $uri->withPath($path));
        $this->assertEquals('/' . $path, (string) $uri);
        $path = '/' . $path;
        $this->assertInstanceOf(UriInterface::class, $uri->withPath($path));
        $uri = $uri->withPath($path);
        $this->assertEquals($path, $uri->getPath());
        $this->assertEquals($path, (string) $uri);
    }

    public function testQueryMethods(): void
    {
        $query = 'foo=bar';

        $uri = new Uri(query: $query);
        $this->assertEquals($uri, $uri->withQuery($query));
        $query .= '&biz=baz';
        $this->assertInstanceOf(UriInterface::class, $uri->withQuery($query));
        $uri = $uri->withQuery($query);
        $this->assertEquals($query, $uri->getQuery());
        $this->assertEquals('?' . $query, (string) $uri);
    }

    public function testFragmentMethods(): void
    {
        $fragment = 'divId1';

        $uri = new Uri(fragment: $fragment);
        $this->assertEquals($uri, $uri->withFragment($fragment));
        $fragment = 'divId2';
        $this->assertInstanceOf(UriInterface::class, $uri->withFragment($fragment));
        $uri = $uri->withFragment($fragment);
        $this->assertEquals($fragment, $uri->getFragment());
        $this->assertEquals('#' . $fragment, (string) $uri);
    }

    public function testFullUri(): void
    {
        $scheme = 'https';
        $host = 'djinn.dev';
        $port = 8080;
        $path = 'path';
        $query = 'foo=bar';
        $fragment = 'divId';
        $user = 'user';
        $password = 'pass';

        $uri = new Uri(
            'https',
            'djinn.dev',
            8080,
            'path',
            'foo=bar',
            'divId',
            'user',
            'pass',
        );
        $fullUri = $scheme . '://' . $user . ':' . $password . '@' . $host . ':' . $port . '/' . $path . '?' . $query . '#' . $fragment;
        $this->assertEquals($fullUri, (string) $uri);
    }
}
