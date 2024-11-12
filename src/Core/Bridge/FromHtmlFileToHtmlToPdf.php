<?php

declare(strict_types=1);

namespace KNPLabs\Core\Bridge;

use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlToPdf;
use KNPLabs\Snappy\Core\Backend\Options;
use Psr\Http\Message\StreamInterface;

final class FromHtmlFileToHtmlToPdf implements HtmlFileToPdf
{
    public function __construct(private HtmlToPdf $adapter) {}

    public function generateFromHtmlFile(\SplFileInfo $file): StreamInterface
    {
        $html = file_get_contents($file->getPathname());

        if (false === $html) {
            throw new \RuntimeException('Unable to read file.');
        }

        return $this->adapter->generateFromHtml($html);
    }

    public function withOptions(callable|Options $options): self
    {
        return new self($this->adapter->withOptions($options));
    }
}
