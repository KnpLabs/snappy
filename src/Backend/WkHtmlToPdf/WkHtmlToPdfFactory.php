<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Backend\Factory;
use KNPLabs\Snappy\Core\Backend\Options;
use SplFileInfo;

/**
 * @implements Factory<WkHtmlToPdfAdapter>
 */
final class WkHtmlToPdfFactory implements Factory
{
    /**
     * @param non-empty-string $binary
     * @param positive-int  $timeout
     */
    public function __construct(private readonly string $binary, private readonly int $timeout)
    {

    }

    public function create(Options $options): Adapter
    {
        return new WkHtmlToPdfAdapter(
            $this->binary,
            $this->timeout,
            $this,
            $options,
        );
    }
}
