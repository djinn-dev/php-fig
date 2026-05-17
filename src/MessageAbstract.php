<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

use function array_keys;
use function array_merge;
use function implode;
use function is_array;
use function is_string;
use function mb_strtolower;

abstract class MessageAbstract implements MessageInterface
{
    private const array VALID_PROTOCOL_VERSIONS = [
        '0.9' => false, // Obsolete
        '1.0' => false, // Obsolete
        '1.1' => true,
        '2' => true,
        '3' => true,
    ];

    private string $protocolVersion = '1.1';

    /** @var string[] */
    private array $headerNameMap = [];

    /** @var string[][] */
    private array $headers = [];

    private StreamInterface $body;

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
        if (!isset(self::VALID_PROTOCOL_VERSIONS[$version]))
        {
            throw new InvalidArgumentException('Invalid portocol.');
        }

        if (!self::VALID_PROTOCOL_VERSIONS[$version])
        {
            throw new InvalidArgumentException('Protocol version is obsolete.');
        }

        if ($version !== $this->protocolVersion)
        {
            $clone = clone $this;
            $clone->protocolVersion = $version;
            return $clone;
        }

        return $this;
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
        return (bool) $this->getHeaderName($name);
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

        return implode(',', $header);
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

        if (in_array($value, $this->getHeader($name)))
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
        if ($body === $this->body)
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
    private function getHeaderName(string $name): string|null
    {
        $lcName = mb_strtolower($name);
        return $this->headerNameMap[$lcName] ?? null;
    }

    /**
     * Convert $value from string to an array if not already an array
     *
     * @param string|array $value
     * @return array
     */
    private function getValueAsArray(string|array $value): array
    {
        if (is_string($value))
        {
            $value = [$value];
        }

        return array_keys($value);
    }

    /**
     * Check if given header has the exact value given
     *
     * @param string $name Case-insensitive
     * @param string|array $value
     * @return boolean
     */
    private function getHeaderIsValue(string $name, string|array $value): bool
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
    private function setHeader(string $name, string|array $value): void
    {
        $lcName = mb_strtolower($name);
        $this->headerNameMap[$lcName] = $name;

        $value = $this->getValueAsArray($value);
        $this->headers[$name] = [];
        foreach ($value as $val)
        {
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
    private function appendToHeader(string $name, string|array $value): void
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
    private function removeHeader(string $name): void
    {
        $lcName = mb_strtolower($name);
        unset($this->headerNameMap[$lcName]);
        unset($this->headers[$name]);
    }
}
