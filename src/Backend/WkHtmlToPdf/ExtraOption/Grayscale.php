<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * PDF will be generated in grayscale.
 */
final class Grayscale extends ExtraOption
{
    public function __construct()
    {
        parent::__construct(
            repeatable: false,
            command: ['--grayscale']
        );
    }
}
