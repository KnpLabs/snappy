<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Frontend;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Exception\FileReadException;
use KNPLabs\Snappy\Core\Exception\FrontendUnsupportedBackendException;
use KNPLabs\Snappy\Core\Stream\FileStream;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class HtmlFileToPdf implements Adapter\HtmlFileToPdf
{
    public function __construct(private readonly Adapter $adapter, private readonly StreamFactoryInterface $streamFactory) {}

    public function withOptions(callable|Options $options): static
    {
        return new self(
            $this->adapter->withOptions($options),
            $this->streamFactory
        );
    }

    public function generateFromHtmlFile(\SplFileInfo $file): StreamInterface
    {
        if ($this->adapter instanceof Adapter\HtmlFileToPdf) {
            return $this->adapter->generateFromHtmlFile($file);
        }

        if ($this->adapter instanceof Adapter\StreamToPdf) {
            return $this->adapter->generateFromStream(
                new FileStream(
                    $file,
                    $this->streamFactory->createStreamFromFile($file->getPathname()),
                ),
            );
        }

        if ($this->adapter instanceof Adapter\HtmlToPdf) {
            $html = file_get_contents($file->getPathname());

            if (false === $html) {
                throw new FileReadException($file);
            }

            return $this->adapter->generateFromHtml($html);
        }

        if ($this->adapter instanceof Adapter\DOMDocumentToPdf) {
            $document = new \DOMDocument();
            $document->loadHTMLFile($file->getPathname());

            return $this->adapter->generateFromDOMDocument($document);
        }

        throw new FrontendUnsupportedBackendException(
            self::class,
            $this->adapter::class,
        );
    }
}
