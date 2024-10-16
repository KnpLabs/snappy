<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Backend;

interface Adapter
{
    /**
     * @param (callable(Options $options): Options)|Options $options
     */
    public function withOptions(callable|Options $options): static;
}
