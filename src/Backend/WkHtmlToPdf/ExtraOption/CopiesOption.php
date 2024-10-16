<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class CopiesOption implements ExtraOption
{
    public function __construct(private readonly int $number) {}

    public function compile(): array
    {
        return ['--copies', $this->number];
    }
}
