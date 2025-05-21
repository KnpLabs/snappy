<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * For each level of headings in the toc the font is scaled by this factor.
 *
 * Default: 0.8
 */
final class TocTextSizeShrink extends ExtraOption
{
    public function __construct(float $scale)
    {
        parent::__construct(
            repeatable: false,
            command: ['--toc-text-size-shrink', (string) $scale]
        );
    }
}
