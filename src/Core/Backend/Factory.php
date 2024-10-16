<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Core\Backend;

/**
 * @template TAdapter of Adapter
 */
interface Factory
{
    /**
     * @return TAdapter
     */
    public function create(Options $options): Adapter;
}
