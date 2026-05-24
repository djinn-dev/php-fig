<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

use function array_merge;
use function implode;
use function is_array;
use function is_string;
use function preg_match;
use function strtolower;

/**
 * @inheritDoc
 */
abstract class MessageAbstract implements MessageInterface
{
    protected const array VALID_PROTOCOL_VERSIONS = [
        '0.9' => false, // Obsolete
        '1.0' => false, // Obsolete
        '1.1' => true,
        '2' => true,
        '3' => true,
    ];

    protected StreamInterface $body;

    protected string $protocolVersion = '1.1';

    protected array $headers = [];

    /** @var string[] */
    protected array $headerNameMap = [];

    public function __clone()
    {
        if (isset($this->body))
        {
            $this->body = clone $this->body;
        }
    }

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion(string $version): MessageInterface
    {
        if ($version === $this->protocolVersion)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->protocolVersion = $version;
        $clone->validateProtocolVersion();

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader(string $name): bool
    {
        return $this->getHeaderName($name) !== null;
    }

    /**
     * @inheritDoc
     */
    public function getHeader(string $name): array
    {
        $name = $this->getHeaderName($name);
        if ($name === null)
        {
            return [];
        }

        return $this->headers[$name];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine(string $name): string
    {
        $header = $this->getHeader($name);

        return implode(', ', $header);
    }

    /**
     * @inheritDoc
     */
    public function withHeader(string $name, mixed $value): MessageInterface
    {
        if (!is_string($value) && !is_array($value))
        {
            throw new InvalidArgumentException('Header value type should be an array or string.');
        }

        if ($this->getHeaderIsValue($name, $value))
        {
            return $this;
        }

        $clone = clone $this;
        $clone->setHeader($name, $value);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader(string $name, $value): MessageInterface
    {
        if (!is_string($value) && !is_array($value))
        {
            throw new InvalidArgumentException('Header value type should be an array or string.');
        }

        $value = $this->getValueAsArray($value);

        if ($this->getHeader($name) === $value)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->appendToHeader($name, $value);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader(string $name): MessageInterface
    {
        if (!$this->hasHeader($name))
        {
            return $this;
        }

        $clone = clone $this;
        $clone->removeHeader($name);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        if (isset($this->body) && $body === $this->body)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    /**
     * Get case-sensitive header name from case-insensitive header name
     *
     * @param string $name Case-insensitive
     * @return string|null  Case-sensitive
     */
    protected function getHeaderName(string $name): string|null
    {
        $lcName = strtolower($name);
        return $this->headerNameMap[$lcName] ?? null;
    }

    /**
     * Convert $value from string to an array if not already an array
     *
     * @param string|array $value
     * @return array
     */
    protected function getValueAsArray(string|array $value): array
    {
        if (is_string($value))
        {
            $value = [$value];
        }

        return array_values($value);
    }

    /**
     * Check if given header has the exact value given
     *
     * @param string $name Case-insensitive
     * @param string|array $value
     * @return boolean
     */
    protected function getHeaderIsValue(string $name, string|array $value): bool
    {
        $value = $this->getValueAsArray($value);

        $header = $this->getHeader($name);
        return ($header === $value);
    }

    /**
     * Set header to the exact value given
     *
     * @param string $name Case-insensitive
     * @param string|array $value
     * @return void
     */
    protected function setHeader(string $name, string|array $value): void
    {
        if (
            $name == ''
            /*
             * RFC token chars:
             * ! # $ % & ' * + - . ^ _ ` | ~ 0-9 A-Z a-z
             */
            || preg_match("/^[!#$%&'*+\-.^_`|~0-9A-Za-z]+$/", $name) !== 1
        ) {
            throw new InvalidArgumentException('Invalid header name');
        }

        $lcName = strtolower($name);
        $this->headerNameMap[$lcName] = $name;

        $value = $this->getValueAsArray($value);

        $this->headers[$name] = [];
        foreach ($value as $val)
        {
            if (
                !is_string($val)
                /*
                 * RFC 7230 field-content allows:
                 * - HTAB: \x09
                 * - SP: \x20
                 * - visible ASCII: \x21-\x7E
                 * - obs-text: \x80-\xFF
                 *
                 * It must not contain CR, LF, NUL, or other control chars.
                 */
                || preg_match('/^[\x09\x20-\x7E\x80-\xFF]*$/', $val) === 0
            ) {
                throw new InvalidArgumentException('Invalid header value');
            }

            $this->headers[$name][] = $val;
        }
    }

    /**
     * Append value to header if existing, otherwise set header to value given
     *
     * @param string $name Case-insensitive
     * @param string|array $value
     * @return void
     */
    protected function appendToHeader(string $name, string|array $value): void
    {
        if ($this->hasHeader($name))
        {
            $value = $this->getValueAsArray($value);
            $value = array_merge($this->getHeader($name), $value);
        }

        $this->setHeader($name, $value);
    }

    /**
     * Remove header entirely
     *
     * @param string $name Case-insensitive
     * @return void
     */
    protected function removeHeader(string $name): void
    {
        $lcName = strtolower($name);
        unset($this->headerNameMap[$lcName]);
        unset($this->headers[$name]);
    }

    /**
     * Validate protocol version and throw if invalid
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateProtocolVersion(): void
    {
        if (!isset(self::VALID_PROTOCOL_VERSIONS[$this->protocolVersion]))
        {
            throw new InvalidArgumentException('Invalid portocol.');
        }

        if (!self::VALID_PROTOCOL_VERSIONS[$this->protocolVersion])
        {
            throw new InvalidArgumentException('Protocol version is obsolete.');
        }
    }
}
