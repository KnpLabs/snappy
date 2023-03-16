<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Option;

use KnpLabs\Snappy\Option;

class KeyValueOption implements Option
{
    public function __construct(
        private string $name,
        private string $key,
        private string $value,
    ) {
    }

    public function getIdentifier(): string
    {
        return "{$this->name}.{$this->key}";
    }

    public function __toString()
    {
        $value = \escapeshellarg($this->value);

        return "--{$this->name} {$this->key} {$value}";
    }
}
