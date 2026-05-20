<?php

declare(strict_types=1);

namespace DjinnDev\Utilities;

use LogicException;

trait SingletonTrait
{
    private static ?self $instance = null;

    /**
     * @return static
     */
    final public static function getInstance(): static
    {
        return static::$instance ??= new static();
    }

    /**
     * @return void
     */
    final public function __clone(): void
    {
    }

    /**
     * @return void
     */
    final public function __wakeup(): void
    {
        throw new LogicException('Cannot unserialize singleton.');
    }
}
