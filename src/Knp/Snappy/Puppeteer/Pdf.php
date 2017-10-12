<?php

declare(strict_types=1);

namespace Knp\Snappy\Puppeteer;

/**
 * PDF generator based on Puppeteer.
 *
 * @author Barry vd. Heuvel <barry@fruitcake.nl>
 */
final class Pdf extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    protected function getAction(): string
    {
        return 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultExtension(): string
    {
        return 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOptions([
            'scale'               => null,
            'displayHeaderFooter' => null,
            'printBackground'     => null,
            'landscape'           => null,
            'pageRanges'          => null,
            'format'              => null,
            'width'               => null,
            'height'              => null,
            'margin'              => null,
        ]);
    }
}
