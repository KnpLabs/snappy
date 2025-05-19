<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Path to the ssl client cert public key in OpenSSL PEM format, optionally followed by intermediate ca and trusted certs.
 */
final class SslCrtPath extends ExtraOption
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(string $path)
    {
        parent::__construct(
            repeatable: false,
            command: ['--ssl-crt-path', $path]
        );
    }
}
