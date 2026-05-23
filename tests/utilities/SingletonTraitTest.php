<?php

declare(strict_types=1);

use DjinnDev\Utilities\SingletonTrait;
use PHPUnit\Framework\TestCase;

final class SingletonTraitTest extends TestCase
{
    public function testSingletonLoansClass(): void
    {
        $testClass = new class () {
            use SingletonTrait;
        };

        $this->assertSame($testClass::getInstance(), $testClass::getInstance());
    }
}
