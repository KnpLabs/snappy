<?php

declare(strict_types=1);

namespace Tests\Unit\__Application__\Http\Cache\SharedCacheConfiguration;

use KnpLabs\Snappy\Bundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
  public function testItProcessesAMinimalWkhtmltopdfConfiguration(): void
  {
    $config = (new Processor())->processConfiguration(
      new Configuration(),
      [[
        'backends' => [
          'my_minimally_configured_wkhtmltopdf_backend' => [
            'driver' => 'wkhtmltopdf',
            'binary_path' => '/usr/bin/wkhtmltopdf',
          ],
        ],
      ]]
    );

    $expected = [
      'backends' => [
        'my_minimally_configured_wkhtmltopdf_backend' => [
          'driver' => 'wkhtmltopdf',
          'timeout' => 30,
          'binary_path' => '/usr/bin/wkhtmltopdf',
          'options' => [],
        ],
      ],
    ];

    $this->assertEquals($config, $expected);
  }

  public function testItProcessesAFullWkhtmltopdfConfiguration(): void
  {
    $config = (new Processor())->processConfiguration(
      new Configuration(),
      [[
        'backends' => [
          'my_fully_configured_wkhtmltopdf_backend' => [
            'driver' => 'wkhtmltopdf',
            'timeout' => 60,
            'binary_path' => '/usr/bin/wkhtmltopdf',
            'options' => [
                'key1' => 'val',
                'key2' => null,
                'key3',
            ],
          ],
        ],
      ]]
    );

    $expected = [
      'backends' => [
        'my_fully_configured_wkhtmltopdf_backend' => [
          'driver' => 'wkhtmltopdf',
          'timeout' => 60,
          'binary_path' => '/usr/bin/wkhtmltopdf',
          'options' => [
              'key1' => 'val',
              'key2' => null,
              'key3',
          ],
        ],
      ],
    ];

    $this->assertEquals($config, $expected);
  }

  public function testItProcessesAMinimalChromiumConfiguration(): void
  {
    $config = (new Processor())->processConfiguration(
      new Configuration(),
      [[
        'backends' => [
          'my_minimally_configured_chromium_backend' => [
            'driver' => 'chromium',
            'binary_path' => '/usr/bin/chromium',
          ],
        ],
      ]]
    );

    $expected = [
      'backends' => [
        'my_minimally_configured_chromium_backend' => [
          'driver' => 'chromium',
          'timeout' => 30,
          'binary_path' => '/usr/bin/chromium',
          'options' => [],
        ],
      ],
    ];

    $this->assertEquals($config, $expected);
  }

  public function testItProcessesAFullChromiumConfiguration(): void
  {
    $config = (new Processor())->processConfiguration(
      new Configuration(),
      [[
        'backends' => [
          'my_fully_configured_chromium_backend' => [
            'driver' => 'chromium',
            'timeout' => 60,
            'binary_path' => '/usr/bin/chromium',
            'options' => [
                'key1' => 'val',
                'key2' => null,
                'key3',
            ],
          ],
        ],
      ]]
    );

    $expected = [
      'backends' => [
        'my_fully_configured_chromium_backend' => [
          'driver' => 'chromium',
          'timeout' => 60,
          'binary_path' => '/usr/bin/chromium',
          'options' => [
              'key1' => 'val',
              'key2' => null,
              'key3',
          ],
        ],
      ],
    ];

    $this->assertEquals($config, $expected);
  }

  public function testItProcessesAMultiBackendConfiguration(): void
  {
    $config = (new Processor())->processConfiguration(
      new Configuration(),
      [[
        'backends' => [
          'my_minimally_configured_wkhtmltopdf_backend' => [
            'driver' => 'wkhtmltopdf',
            'binary_path' => '/usr/bin/wkhtmltopdf',
          ],
          'my_fully_configured_wkhtmltopdf_backend' => [
            'driver' => 'wkhtmltopdf',
            'timeout' => 60,
            'binary_path' => '/usr/bin/wkhtmltopdf',
            'options' => [
              'key1' => 'val',
              'key2' => null,
              'key3',
            ],
          ],
          'my_fully_configured_chromium_backend' => [
            'driver' => 'chromium',
            'timeout' => 60,
            'binary_path' => '/usr/bin/chromium',
            'options' => [
              'key1' => 'val',
              'key2' => null,
              'key3',
            ],
          ],
        ],
      ]]
    );

    $expected = [
      'backends' => [
        'my_minimally_configured_wkhtmltopdf_backend' => [
          'driver' => 'wkhtmltopdf',
          'timeout' => 30,
          'binary_path' => '/usr/bin/wkhtmltopdf',
          'options' => [],
        ],
        'my_fully_configured_wkhtmltopdf_backend' => [
          'driver' => 'wkhtmltopdf',
          'timeout' => 60,
          'binary_path' => '/usr/bin/wkhtmltopdf',
          'options' => [
            'key1' => 'val',
            'key2' => null,
            'key3',
          ],
        ],
        'my_fully_configured_chromium_backend' => [
          'driver' => 'chromium',
          'timeout' => 60,
          'binary_path' => '/usr/bin/chromium',
          'options' => [
            'key1' => 'val',
            'key2' => null,
            'key3',
          ],
        ],
      ],
    ];

    $this->assertEquals($config, $expected);
  }

  public function testItThrowsWhenProcessingAnInvalidDriverConfiguration(): void
  {
    $this->expectException(InvalidConfigurationException::class);

    (new Processor())->processConfiguration(
      new Configuration(),
      [[
        'backends' => [
          'invalid_backend' => [
            'driver' => 'non-existing-driver',
            'binary_path' => '/usr/bin/non-existing-binary',
          ],
        ],
      ]]
    );
  }

  public function testItThrowsWhenProcessingAnInvalidBinaryPathConfiguration(): void
  {
    $this->expectException(InvalidConfigurationException::class);

    (new Processor())->processConfiguration(
      new Configuration(),
      [[
        'backends' => [
          'invalid_backend' => [
            'driver' => 'wkhtmltopdf',
          ],
        ],
      ]]
    );
  }

  public function testItThrowsWhenProcessingAnInvalidTimeoutConfiguration(): void
  {
    $this->expectException(InvalidConfigurationException::class);

    (new Processor())->processConfiguration(
      new Configuration(),
      [[
        'backends' => [
          'invalid_backend' => [
            'driver' => 'wkhtmltopdf',
            'timeout' => 0,
            'binary_path' => '/usr/bin/wkhtmltopdf',
          ],
        ],
      ]]
    );
  }
}
