<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Option;

use KnpLabs\Snappy\Option;

class SimpleWithoutDashOption implements Option
{
    public function __construct(private string $name, private string $value)
    {
    }

    public function getIdentifier(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        $value = \escapeshellarg($this->value);

        return "{$this->name} {$value}";
    }
}
