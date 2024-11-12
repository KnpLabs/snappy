<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Backend\Adapter;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Backend\Factory;
use KNPLabs\Snappy\Core\Backend\Options;

/**
 * @template TAdapter of Adapter
 */
trait Reconfigurable
{
    /**
     * @var Factory<TAdapter>
     */
    private readonly Factory $factory;

    private readonly Options $options;

    /**
     * @return TAdapter
     */
    public function withOptions(callable|Options $options): static
    {
        if (\is_callable($options)) {
            $options = $options($this->options);
        }

        return $this
            ->factory
            ->create($options)
        ;
    }
}
