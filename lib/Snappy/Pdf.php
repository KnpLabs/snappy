<?php

namespace Snappy;

/**
 * Use this class to transform a html/a url to a pdf
 *
 * @package Snappy
 * @author Matthieu Bontemps<matthieu.bontemps@knplabs.com>
 */
class Pdf extends Media
{
    protected $defaultExtension = 'pdf';
    protected $options = array(
        'ignore-load-errors' => null,                          // old v0.9
        'lowquality' => null,
        'copies' => null,
        'dpi' => null,
    );
    
}
