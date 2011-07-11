<?php

namespace Knp\Snappy;

/**
 * Interface for the medias
 *
 * @package Snappy
 *
 * @author  Matthieu Bontemps <matthieu.bontemps@knplabs.com>
 * @author  Antoine Hérault <antoine.herault@knplabs.com>*
 */
interface MediaInterface
{
    /**
     * Generates the output media file from the specified input HTML file
     *
     * @param  string $input   The input HTML filename or URL
     * @param  string $output  The output media filename
     * @param  array  $options An array of options for this generation only
     */
    function generate($input, $output, array $options, $overwrite);

    /**
     * Generates the output media file from the given HTML
     *
     * @param  string $html    The HTML to be converted
     * @param  string $output  The output media filename
     * @param  array  $options An array of options for this generation only
     */
    function generateFromHtml($html, $output, array $options, $overwrite);

    /**
     * Returns the output of the media generated from the specified input HTML
     * file
     *
     * @param  string $input   The input HTML filename or URL
     * @param  array  $options An array of options for this output only
     *
     * @return string
     */
    function getOutput($input, array $options);

    /**
     * Returns the output of the media generated from the given HTML
     *
     * @param  string $html    The HTML to be converted
     * @param  array  $options An array of options for this output only
     *
     * @return string
     */
    function getOutputFromHtml($html, array $options);
}
