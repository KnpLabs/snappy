<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Frontend;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Exception\DOMDocumentException;
use KNPLabs\Snappy\Core\Exception\FrontendUnsupportedBackendException;
use KNPLabs\Snappy\Core\Filesystem\SplResourceInfo;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class DOMDocumentToPdf implements Adapter\DOMDocumentToPdf
{
    public function __construct(private readonly Adapter $adapter, private readonly StreamFactoryInterface $streamFactory) {}

    public function withOptions(callable|Options $options): static
    {
        return new self(
            $this->adapter->withOptions($options),
            $this->streamFactory,
        );
    }

    public function generateFromDOMDocument(\DOMDocument $document): StreamInterface
    {
        if ($this->adapter instanceof Adapter\DOMDocumentToPdf) {
            return $this->adapter->generateFromDOMDocument($document);
        }

        $html = $document->saveHTML();

        if (false === $html) {
            throw new DOMDocumentException('Unable to read HTML from DOMDocument.');
        }

        if ($this->adapter instanceof Adapter\HtmlToPdf) {
            return $this->adapter->generateFromHtml($html);
        }

        if ($this->adapter instanceof Adapter\StreamToPdf) {
            return $this->adapter->generateFromStream(
                $this->streamFactory->createStream($html)
            );
        }

        if ($this->adapter instanceof Adapter\HtmlFileToPdf) {
            $file = SplResourceInfo::fromTmpFile();

            fwrite($file->resource, $html);

            return $this->adapter->generateFromHtmlFile($file);
        }

        throw new FrontendUnsupportedBackendException(
            self::class,
            $this->adapter::class,
        );
    }
}
