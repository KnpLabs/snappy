<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Backend\ChromeHeadless;

use KnpLabs\Snappy\Core\FileToPdf;
use KnpLabs\Snappy\Core\UriToPdf;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Process\Process;

final class ChromeHeadlessToPdf implements FileToPdf, UriToPdf
{
    public function __construct(private string $binary, private StreamFactoryInterface $streamFactory)
    {
    }

    public function generateFromFile(\SplFileInfo $file, \ArrayAccess|array $options = []): StreamInterface
    {
        $path = $file->getRealPath();

        if (!$path) {
            throw new \RuntimeException('The input file does not exist');
        }

        return $this->generate($path, $options);
    }

    public function generateFromUri(UriInterface $url, \ArrayAccess|array $options = []): StreamInterface
    {
        return $this->generate((string) $url, $options);
    }

    private function generate(string $path, \ArrayAccess|array $options = []): StreamInterface
    {
        $escapedBinary = \escapeshellarg($this->binary);
        $binary = is_executable($escapedBinary) ? $escapedBinary : $this->binary;

        if (array_key_exists('print-to-pdf', $options)) {
            $output = $options['print-to-pdf'];
        } else {
            $output = tempnam(sys_get_temp_dir(), 'snappy_');

            $options['print-to-pdf'] = \escapeshellarg($output);
        }

        $args = [];

        foreach ($options as $key => $value) {
            $args[] = null === $value ? "--{$key}" : "--{$key}={$value}";
        }

        $command = implode(' ', [$binary, ...$args, $path]);

        $process = Process::fromShellCommandline($command);

        $process->run();

        $result = $process->getExitCode();
        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();

        if (0 !== $result) {
            throw new \RuntimeException("The command {$command} returned an exit code {$result}. stdout: {$stdout}. stderr: {$stderr}");
        }

        if (!file_exists($output)) {
            throw new \RuntimeException("The output file {$output} does not exist");
        }

        return $this->streamFactory->createStreamFromFile($output);
    }
}
