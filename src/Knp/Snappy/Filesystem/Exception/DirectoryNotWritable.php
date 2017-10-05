<?php

declare(strict_types=1);

namespace Knp\Snappy\Filesystem\Exception;

use Knp\Snappy\Filesystem\Exception;

/**
 * Exception thrown when a directory is not writable but is expected to be.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 */
class DirectoryNotWritable extends \RuntimeException implements Exception
{
    public static function directory(string $directory): self
    {
        return new self(sprintf("Unable to write in directory: %s\n", $directory));
    }
}
