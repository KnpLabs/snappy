<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Path to ssl client cert private key in OpenSSL PEM format.
 */
final class SslKeyPath extends ExtraOption
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(string $path)
    {
        parent::__construct(
            repeatable: false,
            command: ['--ssl-key-path', $path]
        );
    }
}
