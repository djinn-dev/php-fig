<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @inheritDoc
 */
class Request extends MessageAbstract implements RequestInterface
{
    protected const array VALID_REQUEST_METHODS = [
        'GET' => true,
        'POST' => true,
        'PUT' => true,
        'PATCH' => true,
        'DELETE' => true,
    ];

    protected string $method;

    protected UriInterface $uri;

    /**
     * @inheritDoc
     */
    public function getRequestTarget(): string
    {
        $target = $this->uri->getPath();
        if ($target === '')
        {
            $target = '/';
        }

        $query = $this->uri->getQuery();
        if ($query !== '')
        {
            $target .= '?' . $query;
        }

        return $target;
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        if ($requestTarget === $this->getRequestTarget())
        {
            return $this;
        }

        $clone = clone $this;
        $clone->uri = new Uri($requestTarget);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod(string $method): RequestInterface
    {
        $this->verifyMethod($method);

        if ($method === $this->method)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        if ($uri === $this->uri)
        {
            return $this;
        }

        if (!$preserveHost || !$this->hasHeader('Host'))
        {
            $clone = $this->withoutHeader('Host');
            $setHostHeader = true;
        }
        else
        {
            $clone = clone $this;
            $setHostHeader = false;
        }

        $clone->uri = $uri;

        if ($setHostHeader && $clone->uri->getHost() !== '')
        {
            $host = $clone->uri->getHost();

            $port = $clone->uri->getPort();
            if ($port !== null)
            {
                $host .= ':' . $port;
            }

            $headers = ['Host' => $host] + $clone->getHeaders();
            foreach ($headers as $name => $value)
            {
                $clone = $clone->withHeader($name, $value);
            }
        }

        return $clone;
    }

    protected function verifyMethod(string $method): void
    {
        if (!isset(self::VALID_REQUEST_METHODS[$method]))
        {
            throw new InvalidArgumentException('Invalid porocol.');
        }
    }
}
