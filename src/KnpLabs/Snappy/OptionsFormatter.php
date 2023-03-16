<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

interface OptionsFormatter
{
    public function format(Options $options): string;
}
