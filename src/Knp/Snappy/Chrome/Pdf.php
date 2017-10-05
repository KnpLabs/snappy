<?php

declare(strict_types=1);

namespace Knp\Snappy\Chrome;

/**
 * PDF generator based on chrome.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 */
final class Pdf extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    protected function doGenerate(string $inputUri, string $outputFile, array $options)
    {
        $options = array_merge($this->getOptions(), $options);
        $options['print-to-pdf'] = $outputFile;
        $options['headless'] = true;

        if (array_key_exists('screenshot', $options)) {
            throw new \InvalidArgumentException('Option "screenshot" is not allowed in Pdf generator.');
        }

        $this->getBackend()->run($inputUri, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultExtension(): string
    {
        return 'pdf';
    }
}
