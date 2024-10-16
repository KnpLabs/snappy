<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

class PostFile implements ExtraOption
{
    public function __construct(private readonly string $name, private readonly string $value)
    {
    }

    public function isRepeatable(): bool
    {
        return true;
    }

    public function compile(): array
    {
        return ['--post-file', $this->name, $this->value];
    }
}