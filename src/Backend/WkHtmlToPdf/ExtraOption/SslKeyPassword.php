<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Password to ssl client cert private key.
 */
final class SslKeyPassword extends ExtraOption
{
    /**
     * @param non-empty-string $password
     */
    public function __construct(string $password)
    {
        parent::__construct(
            repeatable: false,
            command: ['--ssl-key-password', $password]
        );
    }
}
