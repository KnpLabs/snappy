<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium;

use KNPLabs\Snappy\Core\Backend\Factory;
use KNPLabs\Snappy\Core\Backend\Options;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @implements Factory<HeadlessChromiumAdapter>
 */
final class HeadlessChromiumFactory implements Factory
{
    public function __construct(
        private readonly string $binary,
        private readonly int $timeout,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly UriFactoryInterface $uriFactory,
    ) {
    }

    public function create(Options $options): HeadlessChromiumAdapter
    {
        return new HeadlessChromiumAdapter(
            $this->binary,
            $this->timeout,
            $this,
            $options,
            $this->streamFactory,
            $this->uriFactory,
        );
    }
}
