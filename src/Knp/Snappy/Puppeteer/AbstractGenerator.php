<?php

declare(strict_types=1);

namespace Knp\Snappy\Puppeteer;

use Knp\Snappy\Exception\GenerationFailed;
use Knp\Snappy\Filesystem;
use Knp\Snappy\Generator;
use Knp\Snappy\LocalGenerator;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Abstract Puppeteer generator.
 *
 * @author Barry vd. Heuvel <barry@fruitcake.nl>
 */
abstract class AbstractGenerator implements Generator, LocalGenerator
{
    /** @var array */
    private $options;

    /** @var string */
    private $nodePath;

    /** @var Filesystem */
    private $filesystem;

    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $env;

    /** @var int */
    private $timeout = false;

    public function __construct(array $options = null, $nodePath = null, array $env = [])
    {
        $this->options = $options ?? [
            'viewport'          => ['width' => 1280, 'height' => 1696],
            'fullPage'          => true,
            'emulateMedia'      => 'screen',
            'printBackground'   => true,
            'format'            => 'a4',
        ];
        $this->nodePath = $nodePath;
        $this->env = !empty($env) ? $env : null;
        $this->filesystem = new Filesystem();
        $this->logger = new NullLogger();
    }

    /**
     * Define the action used, can be 'pdf' or 'screenshot'.
     *
     * @return string
     */
    abstract protected function getAction() : string;

    /**
     * Get the default extension for the temporary files.
     *
     * @return string
     */
    abstract protected function getDefaultExtension(): string;

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the Node Modules path where Puppeteer is installed.
     *
     * @param string $nodePath The path to the node_modules dir
     */
    public function setNodePath(string $nodePath)
    {
        $this->nodePath = $nodePath;
    }

    /**
     * Sets the timeout.
     *
     * @param int $timeout The timeout to set
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param string $name
     */
    public function enableOption(string $name)
    {
        $this->options[$name] = true;
    }

    /**
     * @param string $name
     */
    public function removeOption(string $name)
    {
        unset($this->options[$name]);
    }

    /**
     * Set the default value for a specific option.
     *
     * @param string $name    Option name
     * @param mixed  $default Default value
     */
    public function setOption(string $name, $default)
    {
        $this->options[$name] = $default;
    }

    /**
     * Set default values for some options.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($input, string $output, array $options = [], bool $overwrite = false)
    {
        $options = array_merge($this->getOptions(), $options);
        $command = $this->buildCommand($input, $output, $options);
        $process = new Process($command, null, $this->env, null, $this->timeout);

        $this->logger->info(sprintf('Run puppeteer command: "%s".', $command), [
            'command' => $command,
            'env'     => $this->env,
            'timeout' => $this->timeout,
        ]);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            $this->logger->error(sprintf('Puppeteer process failed during execution.'), [
                'command'  => $command,
                'env'      => $this->env,
                'timeout'  => $this->timeout,
                'exitCode' => $process->getExitCode(),
                'stdout'   => $process->getOutput(),
                'stderr'   => $process->getErrorOutput(),
            ]);

            throw new GenerationFailed('Generation failed', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromHtml($html, string $output, array $options = [], bool $overwrite = false)
    {
        $input = $this->filesystem->createTemporaryFile($html, 'html');

        $this->filesystem->prepareOutput($output, $overwrite);

        $this->generate(sprintf('file://%s', $input), $output, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput($input, array $options = [])
    {
        $output = $this->filesystem->createTemporaryFile(null, $this->getDefaultExtension());

        $this->generate($input, $output, $options);

        return $this->filesystem->getFileContents($output);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputFromHtml($html, array $options = [])
    {
        $input = $this->filesystem->createTemporaryFile($html, 'html');
        $output = $this->filesystem->createTemporaryFile(null, $this->getDefaultExtension());

        $this->generate(sprintf('file://%s', $input), $output, $options);

        return $this->filesystem->getFileContents($output);
    }

    /**
     * @param string $input   URI of the input document used
     * @param string $output  Path to the output file
     * @param array  $options Options and arguments to pass to chrome (empty/false/null options are ignored)
     *
     * @return string
     */
    protected function buildCommand(string $input, string $output, array $options): string
    {
        if ($this->nodePath && is_dir($this->nodePath)) {
            $nodePath = escapeshellarg($this->nodePath);
        } else {
            $nodePath = '`npm root -g`';    // Detect root node path
        }

        return implode(' ', [
            'NODE_PATH=' . $nodePath,
            'node',
            escapeshellarg(dirname(dirname(dirname(dirname(__DIR__)))) . '//resources/puppeteer.js'),
            escapeshellarg($this->getAction()),
            escapeshellarg($input),
            escapeshellarg($output),
            escapeshellarg(json_encode($options)),
        ]);
    }
}
