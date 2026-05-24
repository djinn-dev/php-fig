<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

use function is_int;
use function ltrim;
use function strtolower;

/**
 * @inheritDoc
 */
class Uri implements UriInterface
{
    public const array SCHEME_TYPES = [
        'file' => true,
        'ftp' => true,
        'http' => true,
        'https' => true,
        'imap' => true,
        'irc' => true,
        'ircs' => true,
        'sftp' => true,
    ];

    public const array SCHEME_DEFAULT_PORTS = [
        'ftp' => [
            21 => true,
        ],
        'sftp' => [
            22 => true,
            ],
        'http' => [
            80 => true,
            ],
        'https' => [
            443 => true,
            ],
        'imap' => [
            143 => true,
            993 => true,
            ],
    ];

    /**
     * @param string $scheme
     * @param string $host
     * @param string|null $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     * @param string $user
     * @param string|null $password
     */
    public function __construct(
        protected string $scheme = '',
        protected string $host = '',
        protected int|null $port = null,
        protected string $path = '',
        protected string $query = '',
        protected string $fragment = '',
        protected string $user = '',
        protected string|null $password = null,
    ) {
        $this->normalizeAndValidateScheme();
        $this->normalizeHost();
        $this->validatePort();
    }

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
        if ($this->host === '')
        {
            return '';
        }

        $authority = $this->host;
        $userInfo = $this->getUserInfo();
        if ($userInfo !== '')
        {
            $authority = $userInfo . '@' . $authority;
        }

        if ($this->port !== null)
        {
            $authority .= ':' . $this->port;
        }

        return $authority;
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
        $clone->normalizeAndValidateScheme();

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
        $clone->normalizeHost();

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
        $clone->validatePort();

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

        if ($this->host !== '')
        {

            $uri .= '//';
        }

        $userInfo = $this->getUserInfo();
        if ($userInfo !== '')
        {
            $uri .= $userInfo . '@';
        }

        if ($this->host !== '')
        {

            $uri .= $this->host;
        }

        if (
            is_int($this->port)
            && !isset(self::SCHEME_DEFAULT_PORTS[$this->scheme][$this->port])
        ) {
            $uri .= ':' . $this->port;
        }

        if ($this->path !== '')
        {
            $path = ltrim($this->path, '/');
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

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    private function normalizeAndValidateScheme(): void
    {
        if ($this->scheme === '')
        {
            return;
        }

        $this->scheme = strtolower($this->scheme);

        if (!isset(self::SCHEME_TYPES[$this->scheme]))
        {
            throw new InvalidArgumentException('Unknown scheme type');
        }
    }

    /**
     * @return void
     */
    private function normalizeHost(): void
    {
        if ($this->host === '')
        {
            return;
        }

        $this->host = strtolower($this->host);
    }

    /**
     * Throw if not a valid port
     *
     * @return void
     * @throws InvalidArgumentException
     */
    private function validatePort(): void
    {
        if ($this->port === null)
        {
            return;
        }

        if ($this->port < 1)
        {
            throw new InvalidArgumentException('Port cannot be less than 1');
        }

        if ($this->port > 65_535)
        {
            throw new InvalidArgumentException('Port cannot be greater than 65535');
        }
    }
}
