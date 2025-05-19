<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Change the dpi explicitly (this has no effect on X11 based systems).
 *
 * Default: 96.
 */
final class Dpi extends ExtraOption
{
    /**
     * @param int<0, max> $dpi
     */
    public function __construct(int $dpi)
    {
        parent::__construct(
            repeatable: false,
            command: ['--dpi', (string) $dpi]
        );
    }
}
