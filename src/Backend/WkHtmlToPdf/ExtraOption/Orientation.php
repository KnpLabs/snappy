<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;

/**
 * Set orientation to Landscape or Portrait.
 *
 * Default: Portrait
 */
final class Orientation extends ExtraOption
{
    public function __construct(PageOrientation $pageOrientation)
    {
        parent::__construct(
            repeatable: false,
            command: [
                '--orientation',

                match ($pageOrientation) {
                    PageOrientation::PORTRAIT => 'Portrait',
                    PageOrientation::LANDSCAPE => 'Landscape',
                },
            ],
        );
    }
}
