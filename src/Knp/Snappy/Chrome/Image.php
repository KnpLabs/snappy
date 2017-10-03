<?php

declare(strict_types=1);

namespace Knp\Snappy\Chrome;

/**
 * Image generator based on chrome.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com<
 */
final class Image extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    protected function doGenerate(string $inputUri, string $outputFile, array $options)
    {
        $options = array_merge($this->getOptions(), $options);
        $options['screenshot'] = $outputFile;
        $options['headless'] = true;

        if (array_key_exists('print-to-pdf', $options)) {
            throw new \InvalidArgumentException('Option "print-to-pdf" is not allowed in Pdf generator.');
        }

        $this->getBackend()->run($inputUri, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultExtension(): string
    {
        return 'jpg';
    }
}
