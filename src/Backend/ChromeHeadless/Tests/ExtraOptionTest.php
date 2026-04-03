<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\Tests;

use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ExtraOption\NoSandbox::class)]
#[CoversClass(ExtraOption\DisableGpu::class)]
#[CoversClass(ExtraOption\DisableDevShmUsage::class)]
#[CoversClass(ExtraOption\NoPdfHeaderFooter::class)]
#[CoversClass(ExtraOption\VirtualTimeBudget::class)]
#[CoversClass(ExtraOption\WindowSize::class)]
#[CoversClass(ExtraOption\Timeout::class)]
#[CoversClass(ExtraOption\RunAllCompositorStagesBeforeDraw::class)]
final class ExtraOptionTest extends TestCase
{
    /**
     * @return iterable<array{ExtraOption, non-empty-array<string>}>
     */
    public static function chromeExtraOptionProvider(): iterable
    {
        yield 'NoSandbox' => [
            new ExtraOption\NoSandbox(),
            ['--no-sandbox'],
        ];

        yield 'DisableGpu' => [
            new ExtraOption\DisableGpu(),
            ['--disable-gpu'],
        ];

        yield 'DisableDevShmUsage' => [
            new ExtraOption\DisableDevShmUsage(),
            ['--disable-dev-shm-usage'],
        ];

        yield 'NoPdfHeaderFooter' => [
            new ExtraOption\NoPdfHeaderFooter(),
            ['--no-pdf-header-footer'],
        ];

        yield 'VirtualTimeBudget with 5000ms' => [
            new ExtraOption\VirtualTimeBudget(5000),
            ['--virtual-time-budget=5000'],
        ];

        yield 'VirtualTimeBudget with 0ms' => [
            new ExtraOption\VirtualTimeBudget(0),
            ['--virtual-time-budget=0'],
        ];

        yield 'VirtualTimeBudget with 10000ms' => [
            new ExtraOption\VirtualTimeBudget(10000),
            ['--virtual-time-budget=10000'],
        ];

        yield 'WindowSize 1920x1080' => [
            new ExtraOption\WindowSize('1920,1080'),
            ['--window-size=1920,1080'],
        ];

        yield 'WindowSize 1280x720' => [
            new ExtraOption\WindowSize('1280,720'),
            ['--window-size=1280,720'],
        ];

        yield 'WindowSize 800x600' => [
            new ExtraOption\WindowSize('800,600'),
            ['--window-size=800,600'],
        ];

        yield 'Timeout 30000ms' => [
            new ExtraOption\Timeout(30000),
            ['--timeout=30000'],
        ];

        yield 'Timeout 0ms' => [
            new ExtraOption\Timeout(0),
            ['--timeout=0'],
        ];

        yield 'Timeout 60000ms' => [
            new ExtraOption\Timeout(60000),
            ['--timeout=60000'],
        ];

        yield 'RunAllCompositorStagesBeforeDraw' => [
            new ExtraOption\RunAllCompositorStagesBeforeDraw(),
            ['--run-all-compositor-stages-before-draw'],
        ];
    }

    /**
     * @param non-empty-array<string> $expectedCommand
     */
    #[DataProvider('chromeExtraOptionProvider')]
    public function testChromeExtraOption(ExtraOption $option, array $expectedCommand): void
    {
        self::assertFalse($option->repeatable);
        self::assertSame($expectedCommand, $option->command);
    }
}
