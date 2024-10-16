<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\Options\GlobalOptions;

final class DpiOptions
{
    public function __construct(private readonly int $dpi) {}

    public function compile(): array
    {
        return ['--dpi', $this->dpi];
    }
}
