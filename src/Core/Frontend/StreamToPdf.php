<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Frontend;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Exception\FrontendUnsupportedBackendException;
use KNPLabs\Snappy\Core\Exception\StreamDetachedException;
use KNPLabs\Snappy\Core\Filesystem\SplResourceInfo;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class StreamToPdf implements Adapter\StreamToPdf
{
    public function __construct(private readonly Adapter $adapter, private readonly StreamFactoryInterface $streamFactory) {}

    public function withOptions(callable|Options $options): static
    {
        return new self(
            $this->adapter->withOptions($options),
            $this->streamFactory
        );
    }

    public function generateFromStream(StreamInterface $stream): StreamInterface
    {
        if ($this->adapter instanceof Adapter\StreamToPdf) {
            return $this->adapter->generateFromStream($stream);
        }

        if ($this->adapter instanceof Adapter\HtmlToPdf) {
            return $this->adapter->generateFromHtml((string) $stream);
        }

        if ($this->adapter instanceof Adapter\HtmlFileToPdf) {
            $file = SplResourceInfo::fromTmpFile();

            $input = $stream->detach();

            if (null === $input) {
                throw new StreamDetachedException();
            }

            stream_copy_to_stream($input, $file->resource);

            return $this->adapter->generateFromHtmlFile($file);
        }

        if ($this->adapter instanceof Adapter\DOMDocumentToPdf) {
            $document = new \DOMDocument();
            $document->loadHTML((string) $stream);

            return $this->adapter->generateFromDOMDocument($document);
        }

        throw new FrontendUnsupportedBackendException(self::class, $this->adapter::class);
    }
}
