![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)
![License](https://img.shields.io/badge/license-GPL-green)

# PHP-Fig
A fast PHP8 implementation of PHP-Fig interfaces.

## Interfaces Implemented
- PSR-7
- PSR-17

## Installation
Install the package with Composer:

```bash
composer require djinn-dev/php-fig
```

## Usage

### Example
```php
<?php

declare(strict_types=1);

use DjinnDev\Psr17\UriFactory;

$uri = UriFactory::getInstance()->createUri('https://djinn.dev/');
$target = (string) $uri->withPath('test');

// $target = 'https://djinn.dev/test'
```
