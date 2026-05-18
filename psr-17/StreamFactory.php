<?php

declare(strict_types=1);

namespace DjinnDev\Psr17;

use DjinnDev\Psr7\Stream;
use InvalidArgumentException;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class StreamFactory implements StreamFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createStream(string $content = ''): StreamInterface
    {
        return self::createStreamFromFile('php://memory', 'w+');
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (!isset(Stream::READ_WRITE_HASH['read'][$mode]) && !isset(Stream::READ_WRITE_HASH['write'][$mode]))
        {
            throw new InvalidArgumentException('Invalid file mode provided');
        }

        $resource = @fopen($filename, $mode);
        if ($resource === false)
        {
            throw new RuntimeException('File could not be opened');
        }

        return self::createStreamFromResource($resource);
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
