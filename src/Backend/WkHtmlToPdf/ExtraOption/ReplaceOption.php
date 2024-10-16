<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

final class ReplaceOption implements ExtraOption
{
    public function __construct(public readonly string $name, private readonly string $value) {}

    public function isRepeatable(): bool
    {
        return true;
    }

    public function compile(): array
    {
        return ['--replace', $this->name, $this->value];
    }
}
