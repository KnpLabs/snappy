<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium;

use KNPLabs\Snappy\Core\Backend\Options;
use Psr\Http\Message\StreamFactoryInterface;

final class HeadlessChromiumFactory
{
    public function __construct(
        private StreamFactoryInterface $streamFactory
    ) {}

    public function create(Options $options): HeadlessChromiumAdapter
    {
        return new HeadlessChromiumAdapter($options, $this->streamFactory);
    }
}
