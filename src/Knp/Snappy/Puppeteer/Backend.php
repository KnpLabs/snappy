<?php

declare(strict_types=1);

namespace Knp\Snappy\Puppeteer;

use Knp\Snappy\Exception\GenerationFailed;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Puppeteer backend used by Chrome-based generators.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 * @author Barry vd. Heuvel <barry@fruitcake.nl>
 */
class Backend
{
    /** @var string */
    private $binary;

    /** @var array */
    private $env;

    /** @var int */
    private $timeout;

    /** @var CommandBuilder */
    private $commandBuilder;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param string $binary  Path to chrome binary
     * @param array  $env     Environment variables used to run external processes
     * @param int    $timeout Timeout for the external processes in seconds (default: 10)
     */
    public function __construct(string $binary = null, array $env = [], int $timeout = 10)
    {
        $this->binary = $binary;
        $this->env = !empty($env) ? $env : null;
        $this->timeout = $timeout;
        $this->commandBuilder = new CommandBuilder();
        $this->logger = new NullLogger();
    }

    /**
     * Set the command builder used.
     *
     * @param CommandBuilder $commandBuilder
     */
    public function setCommandBuilder(CommandBuilder $commandBuilder)
    {
        $this->commandBuilder = $commandBuilder;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Run chrome with the specified set of options and input URI.
     *
     * @param string $inputUri Input URI
     * @param array  $options  Array of arguments and options passed to chrome.
     *
     * @throws GenerationFailed If generation failed.
     */
    public function run(string $action, string $inputUri, array $options)
    {
        if ($this->binary) {
            $options['executablePath'] = $this->binary;
        }
        $command = $this->commandBuilder->buildCommand($action, $inputUri, $options);
        $process = new Process($command, null, $this->env, null, $this->timeout);

        $this->logger->info(sprintf('Run chrome backend: "%s".', $command), [
            'command' => $command,
            'env'     => $this->env,
            'timeout' => $this->timeout,
        ]);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            $this->logger->error(sprintf('Chrome backend failed during execution.'), [
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
}
