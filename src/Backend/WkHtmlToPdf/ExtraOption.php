<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

/**
 * @see https://wkhtmltopdf.org/usage/wkhtmltopdf.txt
 */
abstract class ExtraOption
{
    /**
     * @param non-empty-array<string> $command
     */
    public function __construct(private bool $repeatable, private array $command) {}

    final public function isRepeatable(): bool
    {
        return $this->repeatable;
    }

    /**
     * @return non-empty-array<string>
     */
    final public function getCommand(): array
    {
        return $this->command;
    }
}
