<?php

declare(strict_types=1);

namespace DjinnDev\Psr17;

use DjinnDev\Psr7\Uri;
use DjinnDev\Utilities\SingletonTrait;
use InvalidArgumentException;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

use function is_array;

class UriFactory implements UriFactoryInterface
{
    use SingletonTrait;

    /**
     * @inheritDoc
     */
    public function createUri(string $uri = ''): UriInterface
    {
        $parts = parse_url($uri);

        if (!is_array($parts))
        {
            throw new InvalidArgumentException('URI cannot be parsed');
        }

        $uri = new Uri();

        if (isset($parts['scheme']))
        {
            $uri = $uri->withScheme($parts['scheme']);
        }

        if (isset($parts['host']))
        {
            $uri = $uri->withHost($parts['host']);
        }

        if (isset($parts['port']))
        {
            $uri = $uri->withPort((int) $parts['port']);
        }

        if (isset($parts['user']))
        {
            $uri = $uri->withUserInfo($parts['user'], $parts['pass'] ?? null);
        }

        if (isset($parts['path']))
        {
            $uri = $uri->withPath($parts['path']);
        }

        if (isset($parts['query']))
        {
            $uri = $uri->withQuery($parts['query']);
        }

        if (isset($parts['fragment']))
        {
            $uri = $uri->withFragment($parts['fragment']);
        }

        return $uri;
    }
}
