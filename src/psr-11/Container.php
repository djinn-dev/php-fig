<?php

declare(strict_types=1);

namespace DjinnDev\Psr11;

use Psr\Container\ContainerInterface;
use Throwable;

use function array_key_exists;
use function is_callable;

/**
 * @inheritDoc
 */
class Container implements ContainerInterface
{
    /** @var array<string, mixed> */
    private array $resolved = [];

    /**
     * @param array<string, mixed> $definitions
     */
    public function __construct(private array $definitions) {}

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        if (!$this->has($id))
        {
            throw new NotFoundException("No entry was found for '{$id}'.");
        }

        if (array_key_exists($id, $this->resolved))
        {
            return $this->resolved[$id];
        }

        $entry = $this->definitions[$id];

        try
        {
            if (is_callable($entry))
            {
                $entry = $entry($this);
            }
        }
        catch (Throwable $throwable)
        {
            throw new ContainerException(
                "Failed to resolve container entry '{$id}'.",
                previous: $throwable,
            );
        }

        return $this->resolved[$id] = $entry;
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions);
    }
}
