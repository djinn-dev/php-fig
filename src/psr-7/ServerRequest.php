<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

use function is_array;
use function is_object;

/**
 * @inheritDoc
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    protected array $cookieParams = [];

    protected array $queryParams = [];

    /** @var UploadedFileInterface[] */
    protected array $uploadedFiles = [];

    protected array|object|null $parsedBody = null;

    protected array $attributes = [];

    /**
     * @param array $serverParams
     */
    public function __construct(
        protected array $serverParams = [],
    ) {
    }

    public function __clone()
    {
        parent::__clone();

        foreach ($this->uploadedFiles as $key => $item)
        {
            $this->uploadedFiles[$key] = clone $item;
        }
    }

    /**
     * @inheritDoc
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * @inheritDoc
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        if ($cookies === $this->cookieParams)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams(array $query): ServerRequestInterface
    {
        if ($query === $this->queryParams)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * @inheritDoc
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        if ($uploadedFiles === $this->uploadedFiles)
        {
            return $this;
        }

        foreach ($uploadedFiles as $file)
        {
            if (!($file instanceof UploadedFileInterface))
            {
                throw new InvalidArgumentException('Not all files are an instance of UploadedFileInterface');
            }
        }

        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getParsedBody(): array|object|null
    {
        return $this->parsedBody;
    }

    /**
     * @inheritDoc
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        if ($data === $this->parsedBody)
        {
            return $this;
        }

        if (!is_array($data) && !is_object($data) && $data !== null)
        {
            throw new InvalidArgumentException('Data provided is not a valid type');
        }

        $clone = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        if (isset($this->attributes[$name]) && $this->attributes[$name] === $value)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        if (!isset($this->attributes[$name]))
        {
            return $this;
        }

        $clone = clone $this;
        unset($clone->attributes[$name]);

        return $clone;
    }
}
