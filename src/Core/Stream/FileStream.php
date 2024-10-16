<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Core\Stream;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;
use UnexpectedValueException;

final class FileStream implements StreamInterface
{
    use StreamWrapper;

    public function __construct(public readonly SplFileInfo $file, StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    public static function createTmpFile(StreamFactoryInterface $streamFactory): self
    {
        $stream = $streamFactory->createStreamFromResource(\tmpfile());
        $filename = $stream->getMetadata('uri');

        if (false === \is_string($filename)) {
            throw new UnexpectedValueException('Unable to retrieve the uri of the temporary file created.');
        }

        return new self(
            new SplFileInfo($filename),
            $stream
        );
    }
}
