<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Backend;

interface Adapter
{
    /**
     * @param Options|(callable(Options $options): Options) $options
     */
    public function withOptions(Options|callable $options): static;
}
