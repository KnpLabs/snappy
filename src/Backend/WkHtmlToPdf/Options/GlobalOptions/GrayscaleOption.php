<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\Options\GlobalOptions;

final class GrayscaleOption
{
    public function __construct() {}

    public function compile(): array
    {
        return ['--grayscale'];
    }
}
