<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class LowQualityOption implements ExtraOption
{
    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--lowquality'];
    }
}
