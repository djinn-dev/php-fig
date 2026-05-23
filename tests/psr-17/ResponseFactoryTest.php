<?php

declare(strict_types=1);

use DjinnDev\Psr17\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ResponseFactoryTest extends TestCase
{
    public function testCreateResponseMethod(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, ResponseFactory::getInstance()->createResponse());
    }
}
