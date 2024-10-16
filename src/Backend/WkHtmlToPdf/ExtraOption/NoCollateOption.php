<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class NoCollateOption implements ExtraOption
{
    public function __construct() {}

    public function compile(): array
    {
        return ['--no-collate'];
    }
}
