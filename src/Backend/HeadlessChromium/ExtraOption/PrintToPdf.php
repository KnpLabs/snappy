<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;

use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;

class PrintToPdf implements ExtraOption
{
    public function __construct(private readonly string $filePath)
    {
    }

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--print-to-pdf=' . $this->filePath];
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
