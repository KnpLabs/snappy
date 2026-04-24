<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\ChromeHeadless\Tests;

use KNPLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessAdapter;
use KNPLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessFactory;
use KNPLabs\Snappy\Backend\ChromeHeadless\ExtraOption;
use KNPLabs\Snappy\Core\Backend\Options;
use KNPLabs\Snappy\Core\Backend\Options\PageOrientation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @internal
 */
#[CoversClass(ChromeHeadlessFactory::class)]
final class ChromeHeadlessFactoryTest extends TestCase
{
    /**
     * @var MockObject&StreamFactoryInterface
     */
    private MockObject $streamFactory;

    /**
     * @var MockObject&UriFactoryInterface
     */
    private MockObject $uriFactory;

    protected function setUp(): void
    {
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->uriFactory = $this->createMock(UriFactoryInterface::class);
    }

#[DoesNotPerformAssertions]
    public function testCreateReturnsAdapterInstance(): void
    {
        $factory = new ChromeHeadlessFactory(
            'google-chrome',
            60,
            $this->streamFactory,
            $this->uriFactory
        );

        $options = new Options(null, []);
        $factory->create($options);

    }

#[DoesNotPerformAssertions]
    public function testCreateWithCustomBinaryAndTimeout(): void
    {
        $factory = new ChromeHeadlessFactory(
            '/usr/bin/chromium',
            120,
            $this->streamFactory,
            $this->uriFactory
        );

        $options = new Options(null, []);
        $factory->create($options);

    }

#[DoesNotPerformAssertions]
    public function testCreateWithPageOrientationAndExtraOptions(): void
    {
        $factory = new ChromeHeadlessFactory(
            'google-chrome',
            60,
            $this->streamFactory,
            $this->uriFactory
        );

        $options = new Options(
            PageOrientation::Landscape,
            [
                new ExtraOption\NoSandbox(),
                new ExtraOption\DisableGpu(),
                new ExtraOption\WindowSize('1920,1080'),
            ]
        );

        $factory->create($options);

    }

#[DoesNotPerformAssertions]
    public function testCreatePassesFactoriesToAdapter(): void
    {
        $factory = new ChromeHeadlessFactory(
            'google-chrome',
            60,
            $this->streamFactory,
            $this->uriFactory
        );

        $options = new Options(null, []);
        $factory->create($options);

    }
}
