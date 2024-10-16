<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\Options\GlobalOptions;

final class ImageDpiOption
{
    public function __construct(public readonly int $dpi) {}
}
