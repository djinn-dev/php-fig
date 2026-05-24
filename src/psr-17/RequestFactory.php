<?php

declare(strict_types=1);

namespace DjinnDev\Psr17;

use DjinnDev\Psr7\Request;
use DjinnDev\Utilities\SingletonTrait;
use InvalidArgumentException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

use function is_string;

class RequestFactory implements RequestFactoryInterface
{
    use SingletonTrait;

    /**
     * @inheritDoc
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (is_string($uri))
        {
            $uri = UriFactory::getInstance()->createUri($uri);
        }

        return (new Request())
            ->withMethod($method)
            ->withUri($uri);
    }
}
