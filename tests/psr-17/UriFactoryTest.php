<?php

declare(strict_types=1);

use DjinnDev\Psr17\UriFactory;
use DjinnDev\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

final class UriFactoryTest extends TestCase
{
    public function testCreateUriIsUriInterface(): void
    {
        $this->assertInstanceOf(UriInterface::class, UriFactory::getInstance()->createUri('https://tgeene.me/'));
    }

    public function testCreateUriParameters(): void
    {
        foreach (Uri::SCHEME_TYPES as $scheme => $true)
        {
            $url = $scheme . '://tgeene.me';
            $uri = UriFactory::getInstance()->createUri($url);
            $this->assertEquals($scheme, $uri->getScheme());

            if (isset(Uri::SCHEME_DEFAULT_PORTS[$scheme]))
            {
                foreach (Uri::SCHEME_DEFAULT_PORTS[$scheme] as $port => $true)
                {
                    $url .= ':' . $port;
                    $uri = UriFactory::getInstance()->createUri($url);
                    $this->assertEquals($port, $uri->getPort());
                    $this->assertNotEquals($url, (string) $uri);

                    $port += 1;
                    $url .= ':' . $port;
                    $uri = UriFactory::getInstance()->createUri($url);
                    $this->assertEquals($port, $uri->getPort());
                    $this->assertEquals($url, (string) $uri);
                }
            }
        }

        $url = 'https://user:pass@tgeene.me:8080/test.html?foo=bar#frag';
        $uri = UriFactory::getInstance()->createUri($url);
        $this->assertEquals($url, (string) $uri);
    }
}
