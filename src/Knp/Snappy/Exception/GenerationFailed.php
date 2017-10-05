<?php

declare(strict_types=1);

namespace Knp\Snappy\Exception;

use Knp\Snappy\Exception;

/**
 * Exception thrown when a generator backend fails.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 */
class GenerationFailed extends \RuntimeException implements Exception
{
}
