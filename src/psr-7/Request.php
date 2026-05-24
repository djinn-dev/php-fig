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
    public const array VALID_REQUEST_METHODS = [
        'GET' => true,
        'POST' => true,
        'PUT' => true,
        'PATCH' => true,
        'DELETE' => true,
        'HEAD' => true,
        'OPTIONS' => true,
        'TRACE' => true,
        'CONNECT' => true,
    ];

    protected string $requestTarget;

    protected string $method;

    protected UriInterface $uri;

    public function __clone()
    {
        parent::__clone();

        if (isset($this->uri))
        {
            $this->uri = clone $this->uri;
        }
    }

    /**
     * @inheritDoc
     */
    public function getRequestTarget(): string
    {
        return $this->requestTarget;
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        if (isset($this->requestTarget) && $requestTarget === $this->requestTarget)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

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
        if (isset($this->method) && $method === $this->method)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->method = $method;
        $clone->normalizeAndValidateMethod();

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
        if (isset($this->uri) && $uri === $this->uri)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->uri = $uri;

        if (!$preserveHost || !$clone->hasHeader('Host') || $clone->getHeaderLine('Host') === '')
        {
            $clone = $clone->withoutHeader('Host');

            $host = $uri->getHost();
            if ($host !== '')
            {
                $port = $uri->getPort();
                if ($port !== null)
                {
                    $host .= ':' . $port;
                }

                $clone = $clone->withHeader('Host', $host);
            }
        }

        return $clone;
    }

    /**
     * Force method to UPPER and throw if not a valid method
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function normalizeAndValidateMethod(): void
    {
        $this->method = strtoupper($this->method);

        if (!isset(self::VALID_REQUEST_METHODS[$this->method]))
        {
            throw new InvalidArgumentException('Invalid method.');
        }
    }
}
