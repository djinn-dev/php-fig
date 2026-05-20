<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

use function fopen;
use function is_string;

use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_PARTIAL;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_NO_TMP_DIR;
use const UPLOAD_ERR_CANT_WRITE;
use const UPLOAD_ERR_EXTENSION;

/**
 * @inheritDoc
 */
class UploadedFile implements UploadedFileInterface
{
    protected const array ERRORS = [
        UPLOAD_ERR_OK => 1,
        UPLOAD_ERR_INI_SIZE => 1,
        UPLOAD_ERR_FORM_SIZE => 1,
        UPLOAD_ERR_PARTIAL => 1,
        UPLOAD_ERR_NO_FILE => 1,
        UPLOAD_ERR_NO_TMP_DIR => 1,
        UPLOAD_ERR_CANT_WRITE => 1,
        UPLOAD_ERR_EXTENSION => 1,
    ];

    private bool $moved = false;

    /**
     * @param StreamInterface $stream
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected StreamInterface $stream,
        protected ?int $size = null,
        protected int $error = UPLOAD_ERR_OK,
        protected string|null $clientFilename = null,
        protected string|null $clientMediaType = null,
    ) {
        if (!isset(self::ERRORS[$this->error]))
        {
            throw new InvalidArgumentException('Error code provided is invalid');
        }
    }

    public function __clone()
    {
        $this->stream = clone $this->stream;
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

        $destination = new Stream($resource);
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
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    /**
     * @inheritDoc
     */
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }

    /**
     * Verify stream is still valid for interactions
     *
     * @return void
     * @throws RuntimeException
     */
    protected function validateStream(): void
    {
        if (!($this->stream instanceof StreamInterface))
        {
            throw new RuntimeException('No stream specified');
        }

        if ($this->error !== UPLOAD_ERR_OK)
        {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->moved)
        {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }
}
