<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class Outline implements ExtraOption
{
    public function compile(): array
    {
        return ['--outline'];
    }
}