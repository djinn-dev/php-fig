<?php

declare(strict_types=1);

use DjinnDev\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

final class StreamTest extends TestCase
{
    public function testConstruct(): void
    {
        $fileUri = 'php://temp';
        $resource = @fopen($fileUri, 'r');

        $stream = new Stream($resource);
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals(null, $stream->getSize());
        $this->assertNotEquals(null, $stream->getMetadata());
        $this->assertNotEquals([], $stream->getMetadata());
        $this->assertEquals($fileUri, $stream->getMetadata('uri'));
        $this->assertEquals(null, $stream->getMetadata('foo'));
    }

    public function testReadWriteMthods(): void
    {
        foreach (array_merge(Stream::READ_MODES, Stream::WRITE_MODES) as $mode => $true)
        {
            $fileUri = '/tmp/test.txt';
            @unlink($fileUri);
            if (!str_starts_with($mode, 'x'))
            {
                @touch($fileUri);
            }
            $resource = @fopen($fileUri, $mode);
            $this->assertNotFalse($resource, $mode);

            $stream = new Stream($resource);
            if (isset($stream::READ_MODES[$mode]))
            {
                $this->assertTrue($stream->isReadable(), $mode . ' - ' . $stream->getMetadata('mode'));
            }
            else
            {
                $this->assertFalse($stream->isReadable(), $mode . ' - ' . $stream->getMetadata('mode'));
            }
            if (isset($stream::WRITE_MODES[$mode]))
            {
                $this->assertTrue($stream->isWritable(), $mode . ' - ' . $stream->getMetadata('mode'));
            }
            else
            {
                $this->assertFalse($stream->isWritable(), $mode . ' - ' . $stream->getMetadata('mode'));
            }
        }

        $fileUri = 'php://temp';
        $resource = @fopen($fileUri, 'r+');
        $content = 'foobar';

        $stream = new Stream($resource);
        $stream->write($content);
        $stream->seek(0);
        $this->assertEquals($content, $stream->getContents());
        $stream->seek(0);
        $this->assertEquals(substr($content, 0, 3), $stream->read(3));
        $this->assertEquals($content, (string) $stream);
    }

    public function testDetatchMethod(): void
    {
        $fileUri = 'php://temp';
        $resource = @fopen($fileUri, 'r');

        $stream = new Stream($resource);
        $this->assertNotEquals(null, $stream->detach());
        $this->assertEquals(null, $stream->detach());
        $this->assertEquals(null, $stream->getSize());
        $this->assertNotEquals([], $stream->getMetadata());
        $this->assertEquals(null, $stream->getMetadata('foo'));

        $this->expectException(RuntimeException::class);
        $stream->getContents();
    }

    public function testCloseMethod(): void
    {
        $fileUri = 'php://temp';
        $resource = @fopen($fileUri, 'r');

        $stream = new Stream($resource);
        $stream->close();
        $this->assertEquals(null, $stream->getSize());
        $this->assertNotEquals([], $stream->getMetadata());
        $this->assertEquals(null, $stream->getMetadata('foo'));

        $this->expectException(RuntimeException::class);
        $stream->getContents();
    }
}
