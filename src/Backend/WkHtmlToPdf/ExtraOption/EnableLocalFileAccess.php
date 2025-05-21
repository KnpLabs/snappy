<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Allowed conversion of a local file to read in other local files.
 */
final class EnableLocalFileAccess extends ExtraOption
{
    public function __construct(
    ) {
        parent::__construct(
            repeatable: false,
            command: ['--enable-local-file-access']
        );
    }
}
