<?php

declare(strict_types=1);

use DjinnDev\Psr17\StreamFactory;
use DjinnDev\Psr17\UploadedFileFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

final class UploadedFileFactoryTest extends TestCase
{
    public function testCreateUploadedFileMethod(): void
    {
        $stream = StreamFactory::getInstance()->createStream('');

        $this->assertInstanceOf(UploadedFileInterface::class, UploadedFileFactory::getInstance()->createUploadedFile($stream));
    }
}
