<?php

declare(strict_types=1);

namespace Knp\Snappy\Filesystem\Exception;

use Knp\Snappy\Filesystem\Exception;

/**
 * Exception thrown when a directory could not be created but is expected to exist.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 */
class CouldNotCreateDirectory extends \RuntimeException implements Exception
{
    public static function directory(string $path): self
    {
        return new self(sprintf('Could not create directory "%s".', $path));
    }
}
