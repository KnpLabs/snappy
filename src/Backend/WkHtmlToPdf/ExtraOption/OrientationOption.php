<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;

use KNPLabs\Snappy\Backend\WkHtmlToPdf\ExtraOption;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;

final class OrientationOption implements ExtraOption
{
    public function __construct(private readonly ExtraOption\Orientation\Value $orientation) {}

    public function isRepeatable(): bool
    {
        return false;
    }

    public static function fromPageOrientation(PageOrientation $pageOrientation): self
    {
        return new self(
            match ($pageOrientation) {
                PageOrientation::PORTRAIT => ExtraOption\Orientation\Value::PORTRAIT,
                PageOrientation::LANDSCAPE => ExtraOption\Orientation\Value::LANDSCAPE,
            }
        );
    }

    public function compile(): array
    {
        return ['--orientation', $this->orientation->value];
    }
}
