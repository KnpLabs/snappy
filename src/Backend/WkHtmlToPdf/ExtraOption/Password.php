<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class Password implements ExtraOption
{
    public function __construct(private readonly string $password)
    {
    }

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--password', $this->password];
    }
}