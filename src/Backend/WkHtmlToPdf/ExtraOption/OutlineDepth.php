<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

/**
 * Set the depth of the outline.
 *
 * Default: 4
 */
final class OutlineDepth extends ExtraOption
{
    public function __construct(int $depth)
    {
        parent::__construct(
            repeatable: false,
            command: ['--outline-depth', (string) $depth]
        );
    }
}
