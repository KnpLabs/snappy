<?php

declare(strict_types=1);

namespace KNPLabs\Core\Bridge;

use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlToPdf;
use KNPLabs\Snappy\Core\Backend\Factory;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\FileToPdf;
use KNPLabs\Snappy\Core\StringToPdf;
use Psr\Http\Message\StreamInterface;

final class FromHtmlFileToHtmlStringToPdf implements HtmlFileToPdf
{
    public function __construct(private HtmlToPdf $adapter)
    {
    }

    public function generateFromHtmlFile(\SplFileInfo $file): StreamInterface
    {
        $html = file_get_contents($file->getPathname());

        if (false === $html) {
            throw new \RuntimeException('Unable to read file.');
        }

        return $this->adapter->generateFromHtml($html);
    }

    public function withOptions(Options|callable $options): self
    {
        return new self( $this->adapter->withOptions($options));
    }
}
