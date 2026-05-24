<?php

declare(strict_types=1);

use DjinnDev\Psr17\StreamFactory;
use DjinnDev\Utilities\SingletonTrait;
use PHPUnit\Framework\TestCase;

final class SingletonTraitTest extends TestCase
{
    public function testClass(): void
    {
        $testClass = new class () {
            use SingletonTrait;
        };

        $this->assertSame($testClass::getInstance(), $testClass::getInstance());

        $instance = $testClass::getInstance();
        $this->assertNotSame($instance, clone $instance);

        $instance = StreamFactory::getInstance();
        $serialized = serialize($instance);
        $this->expectException(LogicException::class);
        unserialize($serialized);
    }
}
