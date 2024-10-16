<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\Options\GlobalOptions;

final class CopiesOption
{
    public function __construct(private readonly int $number) {}

    public function compile(): array
    {
        return ['--copies', $this->number];
    }
}
