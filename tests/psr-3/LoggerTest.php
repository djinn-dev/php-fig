<?php

declare(strict_types=1);

use DjinnDev\Psr3\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class LoggerTest extends TestCase
{
    public function testLoggerIsLoggerInterface(): void
    {
        $this->assertInstanceOf(LoggerInterface::class, Logger::getInstance());
    }
}
