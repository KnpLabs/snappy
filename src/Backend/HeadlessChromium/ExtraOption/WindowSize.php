<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;

use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;

class WindowSize implements ExtraOption
{
    public function __construct(private readonly int $width, private readonly int $height)
    {
    }

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--window-size', "{$this->width}x{$this->height}"];
    }
}
