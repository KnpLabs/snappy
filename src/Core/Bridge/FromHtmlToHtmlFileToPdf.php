<?php

declare(strict_types=1);

namespace KNPLabs\Core\Bridge;

use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlToPdf;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\FileToPdf;
use KNPLabs\Snappy\Core\Stream\FileStream;
use KNPLabs\Snappy\Core\Stream\TemporaryFileStream;
use KNPLabs\Snappy\Core\StringToPdf;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class FromHtmlToHtmlFileToPdf implements HtmlToPdf
{
    public function __construct(private readonly HtmlFileToPdf $adapter, private readonly StreamFactoryInterface $streamFactory)
    {
    }

    public function generateFromHtml(string $html): StreamInterface
    {
        $temporary = FileStream::createTmpFile($this->streamFactory);

        file_put_contents($temporary->file->getPathname(), $html);

        return $this->adapter->generateFromHtmlFile($temporary->file);
    }

    public function withOptions(Options|callable $options): self
    {
        return new self(
            $this->adapter->withOptions($options),
            $this->streamFactory,
        );
    }
}
