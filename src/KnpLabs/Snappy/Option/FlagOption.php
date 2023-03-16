<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Option;

use KnpLabs\Snappy\Option;

class FlagOption implements Option
{
    public function __construct(
        private string $name,
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return "--{$this->name}";
    }
}
