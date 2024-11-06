<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;

use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;

class DisableFeatures implements ExtraOption
{
    public function __construct(private readonly array $features)
    {
    }

    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--disable-features=' . \implode(',', $this->features)];
    }
}
