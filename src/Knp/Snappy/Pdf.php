<?php

namespace Knp\Snappy;

/**
 * Use this class to transform a html/a url to a pdf
 *
 * @package Snappy
 *
 * @author  Matthieu Bontemps <matthieu.bontemps@knplabs.com>
 * @author  Antoine Hérault <antoine.herault@knplabs.com>
 */
class Pdf extends AbstractGenerator
{
    protected $optionsWithContentCheck = array();

    /**
     * {@inheritDoc}
     */
    public function __construct($binary = null, array $options = array(), array $env = null)
    {
        $this->setDefaultExtension('pdf');
        $this->setOptionsWithContentCheck();

        parent::__construct($binary, $options, $env);
    }

    /**
     * Handle options to transform HTML strings into temporary files containing HTML
     * @param array $options
     * @return array $options Transformed options
     */
    protected function handleOptions(array $options = array())
    {
        foreach ($options as $option => $value) {
            if (null === $value) {
                unset($options[$option]);
                continue;
            }

            if (!empty($value) && array_key_exists($option, $this->optionsWithContentCheck)) {
                $saveToTempFile = !$this->isFile($value) && !$this->isOptionUrl($value);
                $fetchUrlContent = $option === 'xsl-style-sheet' && $this->isOptionUrl($value);

                if ($saveToTempFile || $fetchUrlContent) {
                    $fileContent = $fetchUrlContent ? file_get_contents($value) : $value;
                    $options[$option] = $this->createTemporaryFile($fileContent, $this->optionsWithContentCheck[$option]);
                }
            }
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function generate($input, $output, array $options = array(), $overwrite = false)
    {
        $options = $this->handleOptions($this->mergeOptions($options));

        parent::generate($input, $output, $options, $overwrite);
    }

    /**
     * Convert option content or url to file if it is needed
     * @param $option
     * @return bool
     */
    protected function isOptionUrl($option)
    {
        return (bool)filter_var($option, FILTER_VALIDATE_URL);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->addOptions(array(
            'ignore-load-errors'           => null, // old v0.9
            'lowquality'                   => true,
            'collate'                      => null,
            'no-collate'                   => null,
            'cookie-jar'                   => null,
            'copies'                       => null,
            'dpi'                          => null,
            'extended-help'                => null,
            'grayscale'                    => null,
            'help'                         => null,
            'htmldoc'                      => null,
            'image-dpi'                    => null,
            'image-quality'                => null,
            'manpage'                      => null,
            'margin-bottom'                => null,
            'margin-left'                  => null,
            'margin-right'                 => null,
            'margin-top'                   => null,
            'orientation'                  => null,
            'output-format'                => null,
            'page-height'                  => null,
            'page-size'                    => null,
            'page-width'                   => null,
            'no-pdf-compression'           => null,
            'quiet'                        => null,
            'read-args-from-stdin'         => null,
            'title'                        => null,
            'use-xserver'                  => null,
            'version'                      => null,
            'dump-default-toc-xsl'         => null,
            'dump-outline'                 => null,
            'outline'                      => null,
            'no-outline'                   => null,
            'outline-depth'                => null,
            'allow'                        => null,
            'background'                   => null,
            'no-background'                => null,
            'checkbox-checked-svg'         => null,
            'checkbox-svg'                 => null,
            'cookie'                       => null,
            'custom-header'                => null,
            'custom-header-propagation'    => null,
            'no-custom-header-propagation' => null,
            'debug-javascript'             => null,
            'no-debug-javascript'          => null,
            'default-header'               => null,
            'encoding'                     => null,
            'disable-external-links'       => null,
            'enable-external-links'        => null,
            'disable-forms'                => null,
            'enable-forms'                 => null,
            'images'                       => null,
            'no-images'                    => null,
            'disable-internal-links'       => null,
            'enable-internal-links'        => null,
            'disable-javascript'           => null,
            'enable-javascript'            => null,
            'javascript-delay'             => null,
            'load-error-handling'          => null,
            'load-media-error-handling'    => null,
            'disable-local-file-access'    => null,
            'enable-local-file-access'     => null,
            'minimum-font-size'            => null,
            'exclude-from-outline'         => null,
            'include-in-outline'           => null,
            'page-offset'                  => null,
            'password'                     => null,
            'disable-plugins'              => null,
            'enable-plugins'               => null,
            'print-media-type'             => null,
            'no-print-media-type'          => null,
            'post'                         => null,
            'post-file'                    => null,            
            'proxy'                        => null,
            'radiobutton-checked-svg'      => null,
            'radiobutton-svg'              => null,
            'run-script'                   => null,
            'disable-smart-shrinking'      => null,
            'enable-smart-shrinking'       => null,
            'stop-slow-scripts'            => null,
            'no-stop-slow-scripts'         => null,
            'disable-toc-back-links'       => null,
            'enable-toc-back-links'        => null,
            'user-style-sheet'             => null,
            'username'                     => null,
            'window-status'                => null,
            'zoom'                         => null,
            'footer-center'                => null,
            'footer-font-name'             => null,
            'footer-font-size'             => null,
            'footer-html'                  => null,
            'footer-left'                  => null,
            'footer-line'                  => null,
            'no-footer-line'               => null,
            'footer-right'                 => null,
            'footer-spacing'               => null,
            'header-center'                => null,
            'header-font-name'             => null,
            'header-font-size'             => null,
            'header-html'                  => null,
            'header-left'                  => null,
            'header-line'                  => null,
            'no-header-line'               => null,
            'header-right'                 => null,
            'header-spacing'               => null,
            'replace'                      => null,
            'disable-dotted-lines'         => null,
            'cover'                        => null,
            'toc'                          => null,
            'toc-depth'                    => null,
            'toc-font-name'                => null,
            'toc-l1-font-size'             => null,
            'toc-header-text'              => null,
            'toc-header-font-name'         => null,
            'toc-header-font-size'         => null,
            'toc-level-indentation'        => null,
            'disable-toc-links'            => null,
            'toc-text-size-shrink'         => null,
            'xsl-style-sheet'              => null,
            'viewport-size'                => null,
            'redirect-delay'               => null, // old v0.9
            'cache-dir'                    => null,
            'keep-relative-links'          => null,
            'resolve-relative-links'       => null,
        ));
    }

    /**
     * Array with options which require to store the content of the option before passing it to wkhtmltopdf
     */
    protected function setOptionsWithContentCheck()
    {
        $this->optionsWithContentCheck = array(
            'header-html'    => 'html',
            'footer-html'    => 'html',
            'cover'          => 'html',
            'xsl-style-sheet'=> 'xsl',
        );
    }
}
