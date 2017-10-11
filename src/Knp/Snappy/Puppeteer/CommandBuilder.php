<?php

declare(strict_types=1);

namespace Knp\Snappy\Puppeteer;

/**
 * Build the commands used by the backend to run puppeteer.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 * @author Barry vd. Heuvel <barry@fruitcake.nl>
 */
class CommandBuilder
{
    /**
     * @param string $action   screenshot or pdf
     * @param string $inputUri URI of the input document used (
     * @param array  $options  Options and arguments to pass to chrome (empty/false/null options are ignored)
     *
     * @return string
     */
    public function buildCommand(string $action, string $inputUri, array $options): string
    {
        return implode(' ', [
            'NODE_PATH=`npm root -g`',
            escapeshellarg(__DIR__ . '/../../../../resources/puppeteer.js'),
            escapeshellarg($action),
            escapeshellarg($inputUri),
            escapeshellarg(json_encode($options)),
        ]);
    }
}
