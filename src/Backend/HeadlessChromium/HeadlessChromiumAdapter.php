<?php

declare(strict_types = 1);

namespace KNPLabs\Snappy\Backend\HeadlessChromium;

use KNPLabs\Snappy\Core\Backend\Adapter\UriToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\Reconfigurable;
use KNPLabs\Snappy\Core\Backend\Options;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Process\Process;
use InvalidArgumentException;
use RuntimeException;

final class HeadlessChromiumAdapter implements UriToPdf
{
    /**
     * @use Reconfigurable<self>
     */
    use Reconfigurable;

    public function __construct(
        private string $binary,
        private int $timeout,
        HeadlessChromiumFactory $factory,
        Options $options,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
        self::validateOptions($options);

        $this->factory = $factory;
        $this->options = $options;
    }

    public function generateFromUri(UriInterface $url): StreamInterface
    {
        $process = new Process(
            command: [
                $this->binary,
                ...$this->compileOptions(),
                (string) $url,
            ],
            timeout: $this->timeout
        );

        $process->run();

        return $this->streamFactory->createStream($this->getPrintToPdfFilePath());
    }

    public function getPrintToPdfFilePath(): string
    {
        $printToPdfOption = \array_filter(
            $this->options->extraOptions,
            fn ($option) => $option instanceof ExtraOption\PrintToPdf
        );

        if (!empty($printToPdfOption)) {
            $printToPdfOption = \array_values($printToPdfOption)[0];

            return $printToPdfOption->getFilePath();
        }

        throw new RuntimeException('Missing option print to pdf.');
    }

    private static function validateOptions(Options $options): void
    {
        $optionTypes = [];

        foreach ($options->extraOptions as $option) {
            if (!$option instanceof ExtraOption) {
                throw new InvalidArgumentException(\sprintf('Invalid option type provided. Expected "%s", received "%s".', ExtraOption::class, \gettype($option) === 'object' ? \get_class($option) : \gettype($option), ));
            }

            if (\in_array($option::class, $optionTypes, true) && !$option->isRepeatable()) {
                throw new InvalidArgumentException(\sprintf('Duplicate option type provided: "%s".', $option::class, ));
            }

            $optionTypes[] = $option::class;
        }
    }

    /**
     * @return array<mixed>
     */
    private function compileOptions(): array
    {
        return \array_reduce(
            $this->options->extraOptions,
            /**
             * @param array<mixed> $carry
             * @param ExtraOption  $extraOption
             *
             * @return array<mixed>
             */
            function (array $carry, $extraOption) {
                if ($extraOption instanceof ExtraOption) {
                    return [
                        ...$carry,
                        ...$extraOption->compile(),
                    ];
                }

                return $carry;
            },
            []
        );
    }
}
