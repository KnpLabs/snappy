<?php

/**
 * Use this class to transform a html/a url to a pdf
 *
 * @package Snappy
 * @author Matthieu Bontemps<matthieu.bontemps@knplabs.com>
 */
class SnappyPdf extends SnappyMedia
{
    protected $defaultExtension = 'pdf';
    protected $options = array(
        'ignore-load-errors' => null,                          // old v0.9
        'lowquality' => true,
        'username' => null,
        'password' => null,
    );
    
}
