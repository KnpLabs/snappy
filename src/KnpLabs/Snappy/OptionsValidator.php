<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

use KnpLabs\Snappy\Exception\InvalidOptionException;

interface OptionsValidator
{
    /**
     * @throws InvalidOptionException
     */
    public function validateOptions(array $options): void;
}
