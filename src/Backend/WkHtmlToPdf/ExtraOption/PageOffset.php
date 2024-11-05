<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class PageOffset implements ExtraOption
{
    /**
     * @param non-negative-int $offset
     */
    public function __construct(private readonly int $offset) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--page-offset', $this->offset];
    }
}
