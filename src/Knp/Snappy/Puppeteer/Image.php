<?php

declare(strict_types=1);

namespace Knp\Snappy\Puppeteer;

/**
 * Image generator based on Puppeteer.
 *
 * @author Barry vd. Heuvel <barry@fruitcake.nl>
 */
final class Image extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    protected function getAction(): string
    {
        return 'screenshot';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultExtension(): string
    {
        $options = $this->getOptions();

        if (isset($options['type'])) {
            return $options['type'];
        }

        return 'jpeg';
    }
}
