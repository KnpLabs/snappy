<?php

declare(strict_types=1);

namespace Knp\Snappy\Puppeteer;

use Knp\Snappy\Filesystem;
use Knp\Snappy\Generator;
use Knp\Snappy\LocalGenerator;

/**
 * Abstract puppeteer generator.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 * @author Barry vd. Heuvel <barry@fruitcake.nl>
 */
abstract class AbstractGenerator implements Generator, LocalGenerator
{
    /** @var array */
    private $options;

    /** @var Backend */
    private $backend;

    /** @var Filesystem */
    private $filesystem;

    /**
     * @param Backend    $backend Puppeteer backend used to generate PDF/screenshot files
     * @param array|null $options Default options for every generation done with this instance
     *                            If null provided: disable-gpu, incognito and window-size (1280x1696)
     */
    public function __construct(Backend $backend = null, array $options = null)
    {
        $this->backend = $backend ?? new Backend();
        $this->options = $options ?? [
            'viewport'          => ['width' => 1280, 'height' => 1696],
            'fullPage'          => true,
            'emulateMedia'      => 'screen',
            'printBackground'   => true,
        ];
        $this->filesystem = new Filesystem();
    }

    /**
     * Run chrome to generate the output file.
     *
     * @param string $inputUri URI of the input document
     *                         (e.g "file://<filename>" or "data:text/html,<urlencoded-html>").
     * @param array  $options  Set of options specific to this generation
     *
     * @throws \InvalidArgumentException When an invalid option is used
     * @throws \RuntimeException         When backend fails to generate the output file
     */
    abstract protected function doGenerate(string $inputUri, array $options);

    abstract protected function getDefaultExtension(): string;

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return Backend
     */
    protected function getBackend(): Backend
    {
        return $this->backend;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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
        $this->filesystem->prepareOutput($output, $overwrite);

        $options['path'] = $output;

        $this->doGenerate($input, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromHtml($html, string $output, array $options = [], bool $overwrite = false)
    {
        $this->filesystem->prepareOutput($output, $overwrite);

        $options['path'] = $output;

        $this->doGenerate(sprintf('data:text/html,%s', rawurlencode($html)), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput($input, array $options = [])
    {
        $temporaryFile = $this->filesystem->createTemporaryFile(null, $this->getDefaultExtension());

        $options['path'] = $temporaryFile;

        $this->doGenerate($input, $options);

        return $this->filesystem->getFileContents($temporaryFile);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputFromHtml($html, array $options = [])
    {
        $temporaryFile = $this->filesystem->createTemporaryFile(null, $this->getDefaultExtension());

        $options['path'] = $temporaryFile;

        $this->doGenerate(sprintf('data:text/html,%s', rawurlencode($html)), $options);

        return $this->filesystem->getFileContents($temporaryFile);
    }
}
