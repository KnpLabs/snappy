<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class JavascriptDelay implements ExtraOption
{
    /**
     * @param non-negative-int $msec
     */
    public function __construct(private readonly int $msec) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--javascript-delay', $this->msec];
    }
}
