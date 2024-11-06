<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium;

use KNPLabs\Snappy\Core\Backend\Adapter\UriToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\Reconfigurable;
use KNPLabs\Snappy\Core\Backend\Options;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class HeadlessChromiumAdapter implements UriToPdf, HtmlFileToPdf, HtmlToPdf
{
    private string $tempDir;

    /**
     * @use Reconfigurable<self>
     */
    use Reconfigurable;

    public function __construct(
        private Options $options,
        private StreamFactoryInterface $streamFactory
    ) {}

    public function generateFromUri(UriInterface $url): StreamInterface
    {
        $this->tempDir = sys_get_temp_dir();

        $command = $this->buildChromiumCommand((string) $url, $this->tempDir);
        $this->runProcess($command);

        return $this->createStreamFromFile($this->tempDir);
    }

    public function generateFromHtmlFile(SplFileInfo $file): StreamInterface
    {
        $htmlContent = file_get_contents($file->getPathname());
        return $this->generateFromHtml($htmlContent);
    }

    public function generateFromHtml(string $html): StreamInterface
    {
        $outputFile = $this->tempDir . '/pdf_output_';
        $htmlFile = $this->tempDir . '/html_input_';
        file_put_contents($htmlFile, $html);

        $command = $this->buildChromiumCommand("file://$htmlFile", $outputFile);
        $this->runProcess($command);

        unlink($htmlFile);
        return $this->createStreamFromFile($outputFile);
    }

    /**
     * @return array<string>
     */
    private function buildChromiumCommand(string $inputUri, string $outputPath): array
    {
        $options = $this->compileConstructOptions();

        return array_merge([
            'chromium',
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--print-to-pdf=' . $outputPath,
        ], $options, [$inputUri]);
    }

    /**
     * @return array<string>
     */
    private function compileConstructOptions(): array
    {
        $constructOptions = $this->options->extraOptions['construct'] ?? [];

        $compiledOptions = [];
        if (is_array($constructOptions)) {
            foreach ($constructOptions as $key => $value) {
                $compiledOptions[] = "--$key=$value";
            }
        }

        return $compiledOptions;
    }

    private function runProcess(array $command): void
    {
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    private function createStreamFromFile(string $filePath): StreamInterface
    {
        $output = file_get_contents($filePath);
        unlink($filePath);

        return $this->streamFactory->createStream($output ?: '');
    }
}
