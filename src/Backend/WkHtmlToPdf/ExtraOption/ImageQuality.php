<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 *  When jpeg compressing images use this quality.
 *
 *  Default: 94
 */
final class ImageQuality extends ExtraOption
{
    /**
     * @param int<0, max> $quality
     */
    public function __construct(int $quality)
    {
        parent::__construct(
            repeatable: false,
            command: ['--image-quality', (string) $quality]
        );
    }
}
