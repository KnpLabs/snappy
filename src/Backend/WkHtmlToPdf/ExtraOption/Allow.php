<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class Allow implements ExtraOption
{
    public function __construct(private readonly string $path)
    {
    }

    public function isRepeatable(): bool
    {
        return true;
    }

    public function compile(): array
    {
        return ['--allow', $this->path];
    }
}