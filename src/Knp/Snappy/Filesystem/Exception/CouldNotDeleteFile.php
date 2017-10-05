<?php

declare(strict_types=1);

namespace Knp\Snappy\Filesystem\Exception;

use Knp\Snappy\Filesystem\Exception;

/**
 * Exception thrown when a file deletion fails.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 */
class CouldNotDeleteFile extends \RuntimeException implements Exception
{
    public static function file(string $path): self
    {
        return new self(sprintf('Could not delete existing file "%s".', $path));
    }
}
