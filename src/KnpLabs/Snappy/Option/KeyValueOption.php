<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Option;

class KeyValueOption
{
    public function __construct(
        private string $key,
        private string $value,
        private string $separator = '=',
    ) {
    }

    public function __toString(): string
    {
        return $this->key . $this->separator . $this->value;
    }
}
