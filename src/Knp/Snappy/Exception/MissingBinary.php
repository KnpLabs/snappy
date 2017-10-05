<?php

declare(strict_types=1);

namespace Knp\Snappy\Exception;

use Knp\Snappy\Exception;
use Throwable;

/**
 * Exception thrown when a generator is executed bas has no binary.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 */
class MissingBinary extends \LogicException implements Exception
{
    public function __construct(
        $message = 'You must define a binary prior to conversion.',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
