<?php

declare(strict_types=1);

namespace Knp\Snappy\Puppeteer;

/**
 * PDF generator based on chrome.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 * @author Barry vd. Heuvel <barry@fruitcake.nl>
 */
final class Pdf extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    protected function doGenerate(string $inputUri, array $options)
    {
        $options = array_merge($this->getOptions(), $options);

        $this->getBackend()->run('pdf', $inputUri, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultExtension(): string
    {
        return 'pdf';
    }
}
