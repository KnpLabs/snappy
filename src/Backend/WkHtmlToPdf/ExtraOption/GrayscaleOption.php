<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class GrayscaleOption implements ExtraOption
{
    public function compile(): array
    {
        return ['--grayscale'];
    }
}
