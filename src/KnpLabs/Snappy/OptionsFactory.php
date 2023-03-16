<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

interface OptionsFactory
{
    public function create(array $options): Options;
}
