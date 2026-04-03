<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Frontend;

use KNPLabs\Snappy\Core\Backend\Adapter;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Exception\FrontendUnsupportedBackendException;
use KNPLabs\Snappy\Core\Filesystem\SplResourceInfo;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class UriToPdf implements Adapter\UriToPdf
{
    public function __construct(private readonly Adapter $adapter, private readonly StreamFactoryInterface $streamFactory) {}

    public function withOptions(callable|Options $options): static
    {
        return new self(
            $this->adapter->withOptions($options),
            $this->streamFactory
        );
    }

    public function generateFromUri(UriInterface $uri): StreamInterface
    {
        if ($this->adapter instanceof Adapter\UriToPdf) {
            return $this->adapter->generateFromUri($uri);
        }

        if ($this->adapter instanceof Adapter\HtmlFileToPdf) {
            // Download URI content to temp file, delegate
            $content = file_get_contents((string) $uri);

            if (false === $content) {
                throw new \RuntimeException(\sprintf('Failed to download URI: %s', (string) $uri));
            }

            $file = SplResourceInfo::fromTmpFile();
            fwrite($file->resource, $content);

            return $this->adapter->generateFromHtmlFile($file);
        }

        throw new FrontendUnsupportedBackendException(
            self::class,
            $this->adapter::class,
        );
    }
}
