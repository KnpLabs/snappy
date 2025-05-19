<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * When embedding images scale them down to this dpi
 * Default: 600.
 */
final class ImageDpi extends ExtraOption
{
    /**
     * @param int<0, max> $dpi
     */
    public function __construct(int $dpi)
    {
        parent::__construct(
            repeatable: false,
            command: ['--image-dpi', (string) $dpi]
        );
    }
}
