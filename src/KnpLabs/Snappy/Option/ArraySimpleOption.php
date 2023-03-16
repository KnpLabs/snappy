<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Option;

class ArraySimpleOption extends SimpleOption
{
    public function getIdentifier(): string
    {
        return "{$this->name}.{$this->value}";
    }
}
