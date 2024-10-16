<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Stream;

use Psr\Http\Message\StreamInterface;

final class FileStream implements StreamInterface
{
    use StreamWrapper;

    public function __construct(public readonly \SplFileInfo $file, StreamInterface $stream)
    {
        $this->stream = $stream;
    }
}
