<?php

declare(strict_types=1);

namespace Knp\Snappy\Exception;

use Knp\Snappy\Exception;

/**
 * Exception thrown when a file exists but was expected to not be.
 */
class FileAlreadyExistsException extends \InvalidArgumentException implements Exception
{
}
