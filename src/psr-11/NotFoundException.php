<?php

declare(strict_types=1);

namespace DjinnDev\Psr11;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface {}
