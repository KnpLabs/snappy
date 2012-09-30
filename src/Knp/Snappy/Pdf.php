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

    protected $hasHtmlHeader = false;
    protected $hasHtmlFooter = false;

    /**
     * {@inheritDoc}
     */
    public function __construct($binary = null, array $options = array())
    {
        $this->setDefaultExtension('pdf');

        parent::__construct($binary, $options);
    }

    /**
     * handle options to transform HTML header-html or footer-html into files contains HTML
     * @param array $options
     * @return array $options Tranformed options
     */
    protected function handleOptions(array $options = array())
    {
        $headerHtml = isset($options['header-html']) ? $options['header-html'] : null;
        if (null !== $headerHtml && !filter_var($headerHtml, FILTER_VALIDATE_URL) && !$this->isFile($headerHtml)) {
            $options['header-html'] = $this->createTemporaryFile($headerHtml, 'html');
            $this->hasHtmlHeader = true;
        }

        $footerHtml = isset($options['footer-html']) ? $options['footer-html'] : null;
        if (null !== $footerHtml && !filter_var($footerHtml, FILTER_VALIDATE_URL) && !$this->isFile($footerHtml)) {
            $options['footer-html'] = $this->createTemporaryFile($footerHtml, 'html');
            $this->hasHtmlFooter = true;
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function generate($input, $output, array $options = array(), $overwrite = false)
    {
        $options = $this->handleOptions($options);

        parent::generate($input, $output, $options, $overwrite);

        // to delete header or footer generated files
        if ($this->hasHtmlHeader) {
            $this->unlink($options['header-html']);
        }

        if ($this->hasHtmlFooter) {
            $this->unlink($options['footer-html']);
        }
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
            'disable-local-file-access'    => null,
            'enable-local-file-access'     => null,
            'minimum-font-size'            => null,
            'exclude-from-outline'         => null,
            'include-in-outline'           => null,
            'page-offset'                  => null,
            'password'                     => null,
            'disable-plugins'              => null,
            'enable-plugins'               => null,
            'post'                         => null,
            'post-file'                    => null,
            'print-media-type'             => null,
            'no-print-media-type'          => null,
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
        ));
    }
}
