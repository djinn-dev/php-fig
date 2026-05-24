<?php

declare(strict_types=1);

use DjinnDev\Psr17\StreamFactory;
use DjinnDev\Psr17\UploadedFileFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

final class UploadedFileFactoryTest extends TestCase
{
    public function testCreateUploadedFileIsUploadedFileInterface(): void
    {
        $stream = StreamFactory::getInstance()->createStream();
        $this->assertInstanceOf(UploadedFileInterface::class, UploadedFileFactory::getInstance()->createUploadedFile($stream));
    }

    public function testCreateUploadedFileParameters(): void
    {
        $stream = StreamFactory::getInstance()->createStream();
        $uploadedFile = UploadedFileFactory::getInstance()->createUploadedFile($stream);
        $this->assertInstanceOf(StreamInterface::class, $uploadedFile->getStream());
        $this->assertEquals($stream, $uploadedFile->getStream());
        $this->assertEquals(null, $uploadedFile->getSize());
        $this->assertEquals(UPLOAD_ERR_OK, $uploadedFile->getError());
        $this->assertEquals(null, $uploadedFile->getClientFilename());
        $this->assertEquals(null, $uploadedFile->getClientMediaType());

        $size = 10;
        $error = UPLOAD_ERR_INI_SIZE;
        $clientFilename = 'foo';
        $clientMediaType = 'bar';
        $uploadedFile = UploadedFileFactory::getInstance()->createUploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
        $this->assertEquals($size, $uploadedFile->getSize());
        $this->assertEquals($error, $uploadedFile->getError());
        $this->assertEquals($clientFilename, $uploadedFile->getClientFilename());
        $this->assertEquals($clientMediaType, $uploadedFile->getClientMediaType());
    }
}
