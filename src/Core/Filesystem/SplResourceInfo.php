<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Filesystem;

final class SplResourceInfo extends \SplFileInfo
{
    /**
     * @param resource $resource
     */
    public function __construct(public readonly mixed $resource)
    {
        $metadata = stream_get_meta_data($this->resource);
        $uri = $metadata['uri'] ?? throw new \RuntimeException('Stream metadata does not contain uri');
        parent::__construct($uri);
    }

    public static function fromTmpFile(): self
    {
        return new self(tmpfile());
    }
}
