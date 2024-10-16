<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class OutlineDepth implements ExtraOption
{
    public function __construct(private readonly ?int $depth = 4)
    {}

    public function compile(): array
    {
        return ['--outline-depth', $this->depth];
    }
}