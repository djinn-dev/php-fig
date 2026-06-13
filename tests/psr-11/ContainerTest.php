<?php

declare(strict_types=1);

use DjinnDev\Psr11\Container;
use DjinnDev\Psr11\ContainerException;
use DjinnDev\Psr11\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class ContainerTest extends TestCase
{
    public function testConstruct(): void
    {
        $container = new Container([]);
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testHasMethod(): void
    {
        $definitions = [
            'config' => [
                'debug' => true,
            ],
        ];

        $container = new Container($definitions);
        $this->assertTrue($container->has(array_key_first($definitions)));
        $this->assertFalse($container->has('foo'));
    }

    public function testGetMethod(): void
    {
        $definitions = [
            stdClass::class => static function (ContainerInterface $container): stdClass {
                return new stdClass();
            },
            'foobar' => static function (): bool {
                return true;
            },
        ];

        $container = new Container($definitions);
        $stdClass = $container->get(stdClass::class);
        $this->assertInstanceOf(stdClass::class, $stdClass);
        $this->assertEquals($stdClass, $container->get(stdClass::class));

        $this->expectException(NotFoundException::class);
        $container->get('foo');

        $this->expectException(ContainerException::class);
        $container->get('foobar');
    }
}
