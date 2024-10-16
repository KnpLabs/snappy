<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class Copies implements ExtraOption
{
    /**
     * @param positive-int $number
     */
    public function __construct(private readonly int $number) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--copies', $this->number];
    }
}
