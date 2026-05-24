<?php

declare(strict_types=1);

namespace DjinnDev\Psr17;

use DjinnDev\Psr7\ServerRequest;
use DjinnDev\Utilities\SingletonTrait;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    use SingletonTrait;

    /**
     * @inheritDoc
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (is_string($uri))
        {
            $uri = UriFactory::getInstance()->createUri($uri);
        }

        return (new ServerRequest(serverParams: $serverParams))
            ->withMethod($method)
            ->withUri($uri);
    }
}
