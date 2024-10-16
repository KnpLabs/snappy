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
        parent::__construct(stream_get_meta_data($this->resource)['uri']);
    }

    public static function fromTmpFile(): self
    {
        return new self(tmpfile());
    }
}
