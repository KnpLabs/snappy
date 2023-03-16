<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Option;

use KnpLabs\Snappy\Option;

class SimpleOption implements Option
{
    public function __construct(
        protected string $name,
        protected string $value,
        protected string $separator = '='
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        $value = \escapeshellarg($this->value);

        return "--{$this->name}{$this->separator}{$value}";
    }
}
