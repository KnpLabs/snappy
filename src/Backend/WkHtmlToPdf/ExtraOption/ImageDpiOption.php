<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class ImageDpiOption
{
    public function __construct(public readonly int $dpi) {}
}
