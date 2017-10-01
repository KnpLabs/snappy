<?php

declare(strict_types=1);

namespace Knp\Snappy\Exception;

use Knp\Snappy\Exception;

/**
 * Exception thrown when a file was excepted but not found.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 */
class FileNotFound extends \RuntimeException implements Exception
{
    /**
     * Creates a FileNotFound instance with a standardized exception message.
     *
     * @param string $path
     *
     * @return FileNotFound
     */
    public static function notFound(string $path): self
    {
        return new self(sprintf('File "%s" not found.', $path));
    }
}
