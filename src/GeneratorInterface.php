<?php

namespace Knp\Snappy;

/**
 * Interface for the media generators.
 *
 * @author  Matthieu Bontemps <matthieu.bontemps@knplabs.com>
 * @author  Antoine Hérault <antoine.herault@knplabs.com>*
 */
interface GeneratorInterface
{
    /**
     * Generates the output media file from the specified input HTML file.
     *
     * @param array|string $input     The input HTML filename or URL
     * @param string       $output    The output media filename
     * @param array        $options   An array of options for this generation only
     * @param bool         $overwrite Overwrite the file if it exists. If not, throw a FileAlreadyExistsException
     *
     * @return void
     */
    public function generate($input, string $output, array $options = [], bool $overwrite = false): void;

    /**
     * Generates the output media file from the given HTML.
     *
     * @param array|string $html      The HTML to be converted
     * @param string       $output    The output media filename
     * @param array        $options   An array of options for this generation only
     * @param bool         $overwrite Overwrite the file if it exists. If not, throw a FileAlreadyExistsException
     *
     * @return void
     */
    public function generateFromHtml($html, string $output, array $options = [], bool $overwrite = false): void;

    /**
     * Returns the output of the media generated from the specified input HTML
     * file.
     *
     * @param array|string $input   The input HTML filename or URL
     * @param array        $options An array of options for this output only
     *
     * @return string
     */
    public function getOutput($input, array $options = []): string;

    /**
     * Returns the output of the media generated from the given HTML.
     *
     * @param array|string $html    The HTML to be converted
     * @param array        $options An array of options for this output only
     *
     * @return string
     */
    public function getOutputFromHtml($html, array $options = []): string;
}
