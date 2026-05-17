<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use Psr\Http\Message\UriInterface;

use function ltrim;

class Uri implements UriInterface
{
    private string $scheme = '';

    private string $authority = '';

    private string $user = '';
    private string|null $password = null;

    private string $host = '';

    private int|null $port = null;

    private string $path = '';

    private string $query = '';

    private string $fragment = '';

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getAuthority(): string
    {
        return $this->authority;
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        $userInfo = $this->user;

        if ($this->password !== null)
        {
            $userInfo .= ':' . $this->password;
        }

        return $userInfo;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function withScheme(string $scheme): UriInterface
    {
        if ($scheme === $this->scheme)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        if ($user === $this->user && $password === $this->password)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withHost(string $host): UriInterface
    {
        if ($host === $this->host)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withPort(?int $port): UriInterface
    {
        if ($port === $this->port)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withPath(string $path): UriInterface
    {
        if ($path === $this->path)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withQuery(string $query): UriInterface
    {
        if ($query === $this->query)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withFragment(string $fragment): UriInterface
    {
        if ($fragment === $this->fragment)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $uri = '';
        if ($this->scheme !== '')
        {
            $uri .= $this->scheme . ':';
        }

        if ($this->authority !== '')
        {
            $uri .= '//' . $this->authority;
        }

        if ($this->path !== '')
        {
            $path =  ltrim($this->path, '/');
            $uri .= '/' . $path;
        }

        if ($this->query !== '')
        {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment !== '')
        {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }
}
