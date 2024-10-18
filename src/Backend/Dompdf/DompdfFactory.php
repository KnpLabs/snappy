<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\Dompdf;

use KNPLabs\Snappy\Core\Backend\Factory;
use KNPLabs\Snappy\Core\Backend\Options;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @implements Factory<DompdfAdapter>
 */
final readonly class DompdfFactory implements Factory
{
    public function __construct(private readonly StreamFactoryInterface $streamFactory)
    {
    }

    public function create(Options $options): DompdfAdapter
    {
        return new DompdfAdapter(
            factory: $this,
            options: $options,
            streamFactory: $this->streamFactory,
        );
    }
}
