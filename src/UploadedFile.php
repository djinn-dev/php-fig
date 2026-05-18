<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class UploadedFile implements UploadedFileInterface
{
    private const array ERRORS = [
        \UPLOAD_ERR_OK => 1,
        \UPLOAD_ERR_INI_SIZE => 1,
        \UPLOAD_ERR_FORM_SIZE => 1,
        \UPLOAD_ERR_PARTIAL => 1,
        \UPLOAD_ERR_NO_FILE => 1,
        \UPLOAD_ERR_NO_TMP_DIR => 1,
        \UPLOAD_ERR_CANT_WRITE => 1,
        \UPLOAD_ERR_EXTENSION => 1,
    ];

    private StreamInterface $stream;

    private int $error = \UPLOAD_ERR_OK;

    private string|null $clientFilename = null;

    private string|null $clientMediaType = null;

    private bool $moved = false;

    /**
     * @param StreamInterface|resource|string $streamOrFile
     * @return UploadedFileInterface
     */
    public function withStream($streamOrFile): UploadedFileInterface
    {
        if ($streamOrFile === $this->stream)
        {
            return $this;
        }

        if (is_string($streamOrFile))
        {
            $streamOrFile = @fopen($streamOrFile, 'r');
            if ($streamOrFile === false)
            {
                throw new RuntimeException('Target file cannot be opened');
            }
        }

        if (is_resource($streamOrFile))
        {
            $streamOrFile = (new Stream())->withResource($streamOrFile);
        }

        if (!($streamOrFile instanceof StreamInterface))
        {
            throw new InvalidArgumentException('Invalid stream or file provided');
        }

        $clone = clone $this;
        $clone->stream = $streamOrFile;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getStream(): StreamInterface
    {
        $this->validateStream();

        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function moveTo(string $targetPath): void
    {
        $this->validateStream();

        if (!is_string($targetPath) || $targetPath === '')
        {
            throw new InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
        }

        $stream = $this->getStream();
        if ($stream->isSeekable())
        {
            $stream->rewind();
        }

        $resource = @fopen($targetPath, 'w');
        if ($resource === false)
        {
            throw new RuntimeException('Target file cannot be opened');
        }

        $destination = (new Stream())->withResource($resource);
        while (!$stream->eof())
        {
            if (!$destination->write($stream->read(1_048_576)))
            {
                break;
            }
        }

        $this->moved = true;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        return $this->stream->getSize();
    }

    /**
     * @inheritDoc
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @param integer $error
     * @return UploadedFileInterface
     */
    public function withError(int $error): UploadedFileInterface
    {
        if ($error === $this->error)
        {
            return $this;
        }

        if (!isset(self::ERRORS[$this->error]))
        {
            throw new InvalidArgumentException('Upload file error status must be an integer value and one of the "UPLOAD_ERR_*" constants');
        }

        $clone = clone $this;
        $clone->error = $error;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    /**
     * @param string $filename
     * @return UploadedFileInterface
     */
    public function withClientFilename(string $filename): UploadedFileInterface
    {
        if ($filename === $this->clientFilename)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->clientFilename = $filename;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }

    /**
     * @param string $mediaType
     * @return UploadedFileInterface
     */
    public function withClientMediaType(string $mediaType): UploadedFileInterface
    {
        if ($mediaType === $this->clientMediaType)
        {
            return $this;
        }

        $clone = clone $this;
        $clone->clientMediaType = $mediaType;

        return $clone;
    }

    /**
     * Verify stream is still valid for interactions
     *
     * @return void
     * @throws RuntimeException
     */
    private function validateStream(): void
    {
        if (!($this->stream instanceof StreamInterface))
        {
            throw new RuntimeException('No stream specified');
        }

        if ($this->error !== \UPLOAD_ERR_OK)
        {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->moved)
        {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }
}
