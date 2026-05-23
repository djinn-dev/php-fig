<?php

declare(strict_types=1);

namespace DjinnDev\Psr17;

use DjinnDev\Psr7\Stream;
use DjinnDev\Utilities\SingletonTrait;
use InvalidArgumentException;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * @inheritDoc
 */
class StreamFactory implements StreamFactoryInterface
{
    use SingletonTrait;

    /**
     * @inheritDoc
     */
    public function createStream(string $content = ''): StreamInterface
    {
        return self::createStreamFromFile('php://temp', 'w+');
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (!isset(Stream::READ_MODES[$mode]) && !isset(Stream::WRITE_MODES[$mode]))
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
