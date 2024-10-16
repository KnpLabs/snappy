<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class FooterCenterOption implements ExtraOption
{
    public function __construct(public readonly string $text) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--footer-center', $this->text];
    }
}
