<?php

declare(strict_types=1);

use DjinnDev\Psr17\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

final class StreamFactoryTest extends TestCase
{
    public function testCreateStreamFromResourceIsStreamInterface(): void
    {
        $this->assertInstanceOf(StreamInterface::class, StreamFactory::getInstance()->createStreamFromResource(@fopen(__FILE__, 'r')));
    }

    public function testCreateStreamFromFileIsStreamInterface(): void
    {
        $this->assertInstanceOf(StreamInterface::class, StreamFactory::getInstance()->createStreamFromFile(__FILE__, 'r'));
    }

    public function testCreateStreamFromFileParameters(): void
    {
        $stream = StreamFactory::getInstance()->createStreamFromFile(__FILE__, 'r');
        $this->assertEquals(file_get_contents(__FILE__), $stream->getContents());

        $this->expectException(InvalidArgumentException::class);
        StreamFactory::getInstance()->createStreamFromFile(__FILE__, 'q');

        $this->expectException(RuntimeException::class);
        StreamFactory::getInstance()->createStreamFromFile(__FILE__ . '.test', 'r');
    }

    public function testCreateStreamIsStreamInterface(): void
    {
        $this->assertInstanceOf(StreamInterface::class, StreamFactory::getInstance()->createStream());
    }

    public function testCreateStreamParameters(): void
    {
        $content = 'foobar';
        $stream = StreamFactory::getInstance()->createStream($content);
        $stream->rewind();
        $this->assertEquals($content, $stream->getContents());
    }
}
