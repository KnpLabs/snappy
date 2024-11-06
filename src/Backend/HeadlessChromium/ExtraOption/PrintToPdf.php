<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;

use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;
use SplFileInfo;

class PrintToPdf implements ExtraOption
{
    public function __construct(private readonly SplFileInfo $file)
    {
    }

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--print-to-pdf=' . $this->file];
    }

    public function getFile(): SplFileInfo
    {
        return $this->file;
    }
}
