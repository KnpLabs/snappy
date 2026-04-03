<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless;

abstract class ExtraOption
{
    /**
     * @param non-empty-array<string> $command
     */
    public function __construct(public readonly bool $repeatable, public readonly array $command) {}
}
