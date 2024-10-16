<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

interface ExtraOption
{
    public function isRepeatable(): bool;

    /** @return array<float|int|string> */
    public function compile(): array;
}
