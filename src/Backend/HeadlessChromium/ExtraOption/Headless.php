<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;

use KNPLabs\Snappy\Backend\HeadlessChromium\ExtraOption;

class Headless implements ExtraOption
{
    public function isRepeatable(): bool
    {
        return false;
    }

    public function compile(): array
    {
        return ['--headless'];
    }
}
