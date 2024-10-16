<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class GrayscaleOption
{
    public function compile(): array
    {
        return ['--grayscale'];
    }
}
