<?php

declare(strict_types=1);

use DjinnDev\Psr17\UriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

final class UriFactoryTest extends TestCase
{
    public function testCreateUriMethod(): void
    {
        $this->assertInstanceOf(UriInterface::class, UriFactory::getInstance()->createUri('http://tgeene.me'));
    }
}
