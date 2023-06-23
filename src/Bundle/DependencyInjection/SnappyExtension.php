<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Bundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class SnappyExtension extends Extension
{
  public function load(array $config, ContainerBuilder $container): void
  {
    foreach($config['backends'] as $backend) {
      // @TODO: load backend services
    }
  }
}
