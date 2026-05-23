<?php

declare(strict_types=1);

use DjinnDev\Psr7\Stream;
use DjinnDev\Psr7\UploadedFile;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

final class UploadedFileTest extends TestCase
{
    public function testConstruct(): void
    {
        $uploadedFile = new UploadedFile($this->getStream());
        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
        $this->assertEquals(null, $uploadedFile->getSize());
        $this->assertEquals(\UPLOAD_ERR_OK, $uploadedFile->getError());
        $this->assertEquals(null, $uploadedFile->getClientFilename());
        $this->assertEquals(null, $uploadedFile->getClientMediaType());
    }

    public function testMoveToMethod(): void
    {
        $stream = $this->getStream();
        $stream->write('here');

        $uploadedFile = new UploadedFile($stream);
        $this->expectException(InvalidArgumentException::class);
        $uploadedFile->moveTo('');

        $uploadedFile->moveTo('/tmp/testMoveToMethod.txt');

        $this->expectException(RuntimeException::class);
        $uploadedFile->moveTo('/tmp/testMoveToMethod.txt');
    }

    private function getStream(): StreamInterface
    {
        $fileUri = 'php://temp';
        $resource = @fopen($fileUri, 'r+');
        return new Stream($resource);
    }
}
