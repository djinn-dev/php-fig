<?php

declare(strict_types=1);

use DjinnDev\Psr17\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

final class StreamFactoryTest extends TestCase
{
    public function testCreateStreamFromResourceMethod(): void
    {
        $resource = @fopen(__FILE__, 'r');

        $this->assertInstanceOf(StreamInterface::class, StreamFactory::getInstance()->createStreamFromResource($resource));
    }

    public function testCreateStreamFromFileMethod(): void
    {
        $this->assertInstanceOf(StreamInterface::class, StreamFactory::getInstance()->createStreamFromFile(__FILE__, 'r'));

        $this->expectException(InvalidArgumentException::class);

        StreamFactory::getInstance()->createStreamFromFile(__FILE__, 'q');

        $this->expectException(RuntimeException::class);

        StreamFactory::getInstance()->createStreamFromFile(__FILE__ . '.test', 'r');
    }

    public function testCreateStreamMethod(): void
    {
        $content = 'foobar';

        $stream = StreamFactory::getInstance()->createStream($content);
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $stream->rewind();
        $this->assertEquals($content, $stream->getContents());
    }
}
