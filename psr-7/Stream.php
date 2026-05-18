<?php

declare(strict_types=1);

namespace DjinnDev\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Throwable;

use function fclose;
use function feof;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function is_resource;
use function restore_error_handler;
use function set_error_handler;
use function stream_get_contents;
use function stream_get_meta_data;

class Stream implements StreamInterface
{
    public const array READ_WRITE_HASH = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true,
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true,
        ],
    ];

    protected array|null $streamMetaData = null;

    protected int|null $size = null;

    /**
     * @param resource $stream
     * @throws InvalidArgumentException
     */
    public function __construct(protected $stream)
    {
        if (!is_resource($this->stream))
        {
            throw new InvalidArgumentException('Instance of Stream requires a valid resource');
        }
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        if ($this->isSeekable())
        {
            $this->seek(0);
        }

        return $this->getContents();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if (isset($this->stream))
        {
            if (is_resource($this->stream))
            {
                fclose($this->stream);
            }

            $this->detach();
        }
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        if (!isset($this->stream))
        {
            return null;
        }

        $result = $this->stream;
        unset($this->stream);

        $this->size = null;
        $this->streamMetaData = null;

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        if ($this->size !== null || !isset($this->stream))
        {
            return $this->size;
        }

        $stats = fstat($this->stream);
        if (isset($stats['size']))
        {
            $this->size = $stats['size'];

            return $this->size;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        if (!isset($this->stream))
        {
            throw new RuntimeException('No stream assigned');
        }

        $position = @ftell($this->stream);
        if ($position === false)
        {
            throw new RuntimeException('Unable to determine stream position');
        }

        return $position;
    }

    /**
     * @inheritDoc
     */
    public function eof(): bool
    {
        return !isset($this->stream) || feof($this->stream);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable(): bool
    {
        $seekable = $this->getMetadata('seekable');
        return ($seekable && fseek($this->stream, 0, \SEEK_CUR) === 0);
    }

    /**
     * @inheritDoc
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->stream))
        {
            throw new RuntimeException('No stream assigned');
        }

        if (!$this->isSeekable())
        {
            throw new RuntimeException('Stream is not seekable');
        }

        $result = fseek($this->stream, $offset, $whence);
        if ($result === -1)
        {
            throw new RuntimeException('Unable to seek to stream position');
        }
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @inheritDoc
     */
    public function isWritable(): bool
    {
        $mode = $this->getMetadata('mode');
        return ($mode !== null && isset(self::READ_WRITE_HASH['write'][$mode]));
    }

    /**
     * @inheritDoc
     */
    public function write(string $string): int
    {
        if (!isset($this->stream))
        {
            throw new RuntimeException('No stream assigned');
        }

        if (!$this->isWritable())
        {
            throw new RuntimeException('Stream is not writable');
        }

        $this->size = null;

        $result = @fwrite($this->stream, $string);
        if ($result === false)
        {
            throw new RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isReadable(): bool
    {
        $mode = $this->getMetadata('mode');
        return ($mode !== null && isset(self::READ_WRITE_HASH['read'][$mode]));
    }

    /**
     * @inheritDoc
     */
    public function read(int $length): string
    {
        if (!isset($this->stream))
        {
            throw new RuntimeException('No stream assigned');
        }

        if (!$this->isReadable())
        {
            throw new RuntimeException('Stream is not readable');
        }

        $result = @fread($this->stream, $length);
        if ($result === false)
        {
            throw new RuntimeException('Unable to read from stream');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
    {
        if (!isset($this->stream))
        {
            throw new RuntimeException('No stream assigned');
        }

        $exception = null;

        set_error_handler(static function ($type, $message) use (&$exception) {
            throw $exception = new RuntimeException('Unable to read stream contents');
        });

        try
        {
            return stream_get_contents($this->stream);
        }
        catch (Throwable $e)
        {
            throw $e === $exception ? $e : new RuntimeException('Unable to read from stream');
        }
        finally
        {
            restore_error_handler();
        }
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(?string $key = null)
    {
        if ($this->streamMetaData === null)
        {
            $this->streamMetaData = stream_get_meta_data($this->stream);
        }

        if ($key === null)
        {
            return $this->streamMetaData;
        }

        return $this->streamMetaData[$key] ?? null;
    }
}
