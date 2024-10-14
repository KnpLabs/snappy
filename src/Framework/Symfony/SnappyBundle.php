<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Framework\Symfony;

use KNPLabs\Snappy\Framework\Symfony\DependencyInjection\SnappyExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SnappyBundle extends Bundle
{
    protected function createContainerExtension(): SnappyExtension
    {
        return new SnappyExtension;
    }
}
