<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class RadiobuttonCheckedSvg implements ExtraOption
{
    public function __construct(private readonly string $path)
    {
    }

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--radiobutton-checked-svg', $this->path];
    }
}