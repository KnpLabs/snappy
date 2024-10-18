<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Core\Backend\Options;

enum PageOrientation: string
{
    case LANDSCAPE = 'landscape';
    case PORTRAIT = 'portrait';
}
