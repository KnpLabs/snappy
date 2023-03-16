<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Option;

use KnpLabs\Snappy\Option;

/**
 * TODO: what about escapeshellarg()?
 */
class SimpleOption implements Option
{
    public function __construct(
        private string $value
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
