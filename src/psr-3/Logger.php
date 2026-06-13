<?php

declare(strict_types=1);

namespace DjinnDev\Psr3;

use DjinnDev\Utilities\SingletonTrait;
use InvalidArgumentException;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;

use function is_string;
use function is_int;
use function is_null;
use function is_scalar;
use function strtr;
use function syslog;

/**
 * @inheritDoc
 */
class Logger extends AbstractLogger
{
    use SingletonTrait;

    /**
     * @inheritDoc
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        if (is_string($level))
        {
            $level = match ($level)
            {
                LogLevel::DEBUG => 7,
                LogLevel::INFO => 6,
                LogLevel::NOTICE => 5,
                LogLevel::WARNING => 4,
                LogLevel::ERROR => 3,
                LogLevel::CRITICAL => 2,
                LogLevel::ALERT => 1,
                LogLevel::EMERGENCY => 0,
                default => -1,
            };
        }

        if (!is_int($level) || $level < 0 || $level > 7)
        {
            throw new InvalidArgumentException('Invalid log level');
        }

        $replace = [];
        foreach ($context as $key => $value)
        {
            if (is_null($value) || is_scalar($value) || $value instanceof Stringable)
            {
                $replace['{' . $key . '}'] = (string) $value;
            }
        }
        $message = strtr((string) $message, $replace);

        syslog($level, $message);
    }
}
