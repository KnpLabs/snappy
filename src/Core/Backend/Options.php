<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Backend;

use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;

final class Options
{
    /**
     * @param array<mixed> $extraOptions
     */
    public function __construct(
        public readonly ?PageOrientation $pageOrientation,
        public readonly array $extraOptions
    ) {
    }

    public static function create(): self
    {
        return new self(
            pageOrientation: null,
            extraOptions: []
        );
    }

    public function withPageOrientation(?PageOrientation $pageOrientation): self
    {
        return new self(
            pageOrientation: $pageOrientation,
            extraOptions: $this->extraOptions,
        );
    }

    /**
     * @param array<mixed> $extraOptions
     */
    public function withExtraOptions(array $extraOptions): self
    {
        return new self(
            pageOrientation: $this->pageOrientation,
            extraOptions: $extraOptions,
        );
    }
}
