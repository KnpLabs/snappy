<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\Reconfigurable;
use KNPLabs\Snappy\Core\Backend\Options;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

final class WkHtmlToPdfAdapter implements HtmlFileToPdf
{
    /**
     * @use Reconfigurable<self>
     */
    use Reconfigurable;

    /**
     * @param non-empty-string $binary
     * @param positive-int  $timeout
     */
    public function __construct(
        private string $binary,
        private int $timeout,
        WkHtmlToPdfFactory $factory,
        Options $options
    ) {
        $this->factory = $factory;
        $this->options = $options;
    }

    public function generateFromHtmlFile(SplFileInfo $file): StreamInterface
    {
        throw new \Exception("Not implemented for {$this->binary} with timeout {$this->timeout}.");
    }
}
