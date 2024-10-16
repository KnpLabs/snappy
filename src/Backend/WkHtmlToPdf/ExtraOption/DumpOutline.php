<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class DumpOutline implements ExtraOption
{
    public function __construct(private readonly string $file) {}

    public function compile(): array
    {
        if (!is_file($this->file)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $this->file));
        }
        return ['--dump-outline', $this->file];
    }
}