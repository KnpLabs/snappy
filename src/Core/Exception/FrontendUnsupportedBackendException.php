<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Exception;

use KNPLabs\Snappy\Core\Exception;

final class FrontendUnsupportedBackendException extends Exception
{
    public function __construct(string $frontendClass, string $backendClass)
    {
        parent::__construct(
            \sprintf(
                'Snappy frontend "%s" does not support backend "%s"',
                $frontendClass,
                $backendClass,
            )
        );
    }
}
