<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

interface Backend
{
    /**
     * @throws InvalidOptionException
     */
    public function validateOptions(array $options): void;
}
