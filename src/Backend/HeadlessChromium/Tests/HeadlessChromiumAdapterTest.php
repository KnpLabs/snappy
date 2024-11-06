<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium\Tests;

use KNPLabs\Snappy\Backend\HeadlessChromium\HeadlessChromiumAdapter;
use KNPLabs\Snappy\Backend\HeadlessChromium\HeadlessChromiumFactory;
use KNPLabs\Snappy\Core\Backend\Options;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class HeadlessChromiumAdapterTest extends TestCase
{
    private Options $options;
    private StreamFactoryInterface $streamFactory;
    private HeadlessChromiumAdapter $adapter;
    private HeadlessChromiumFactory $factory;

    protected function setUp(): void
    {
        $this->options = new Options(null, []);
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->factory = new HeadlessChromiumFactory($this->streamFactory);
        $this->adapter = $this->factory->create($this->options);
    }

    public function testGenerateFromUri(): void
    {
        $url = $this->createMock(UriInterface::class);
        $url->method('__toString')->willReturn('https://example.com');
    }

    public function testGenerateFromHtmlFile(): void
    {
        $file = $this->createMock(\SplFileInfo::class);
        $file->method('getPathname')->willReturn('/path/to/test.html');

        $outputStream = $this->createMock(StreamInterface::class);
        $this->streamFactory->method('createStream')->willReturn($outputStream);

        $process = $this->createMockProcess(['chromium', '--headless', '--print-to-pdf', 'output.pdf'], true);

        $this->adapter->method('runProcess')->willReturnCallback(function ($command) use ($process) {
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        });

        $result = $this->adapter->generateFromHtmlFile($file);

        $this->assertSame($outputStream, $result);
    }

    public function testGenerateFromHtml(): void
    {
        $htmlContent = '<html><body>Hello World</body></html>';

        $outputStream = $this->createMock(StreamInterface::class);
        $this->streamFactory->method('createStream')->willReturn($outputStream);

        $process = $this->createMockProcess(['chromium', '--headless', '--print-to-pdf', 'output.pdf'], true);

        $this->adapter->method('runProcess')->willReturnCallback(function ($command) use ($process) {
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        });

        $result = $this->adapter->generateFromHtml($htmlContent);

        $this->assertSame($outputStream, $result);
    }

    public function testProcessFailsOnInvalidUri(): void
    {
        $url = $this->createMock(UriInterface::class);
        $url->method('__toString')->willReturn('invalid-url');

        $this->expectException(ProcessFailedException::class);

        $process = $this->createMockProcess(['chromium', '--headless', '--print-to-pdf', 'output.pdf'], false);

        $this->adapter->method('runProcess')->willReturnCallback(function ($command) use ($process) {
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        });

        $this->adapter->generateFromUri($url);
    }

    public function testProcessFailsOnEmptyHtml(): void
    {
        $this->expectException(ProcessFailedException::class);

        $process = $this->createMockProcess(['chromium', '--headless', '--print-to-pdf', 'output.pdf'], false);

        $this->adapter->method('runProcess')->willReturnCallback(function ($command) use ($process) {
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        });

        $this->adapter->generateFromHtml('');
    }

    private function createMockProcess(array $command, bool $successful = true): Process
    {
        $process = $this->getMockBuilder(Process::class)
            ->setConstructorArgs([$command])
            ->getMock();

        $process->method('run');
        $process->method('isSuccessful')->willReturn($successful);

        return $process;
    }
}
