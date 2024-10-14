<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\Dompdf;

use ArrayAccess;
use DOMDocument;
use Dompdf;
use KNPLabs\Snappy\Core\Backend\Adapter\DOMDocumentToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\Reconfigurable;
use KNPLabs\Snappy\Core\Backend\Options;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

final readonly class DompdfAdapter implements DOMDocumentToPdf, HtmlFileToPdf, HtmlToPdf
{
    /**
     * @use Reconfigurable<self>
     */
    use Reconfigurable;

    public function __construct(
        DompdfFactory $factory,
        Options $options,
        private readonly StreamFactoryInterface $streamFactory
    ) {
        $this->factory = $factory;
        $this->options = $options;
    }

    public function generateFromDOMDocument(DOMDocument $DOMDocument): StreamInterface
    {
        $dompdf = $this->buildDompdf();
        $dompdf->loadDOM($DOMDocument);

        return $this->createStream($dompdf);
    }

    public function generateFromHtmlFile(SplFileInfo $file): StreamInterface
    {
        $dompdf = $this->buildDompdf();
        $dompdf->loadHtmlFile($file->getPath());

        return $this->createStream($dompdf);
    }

    public function generateFromHtml(string $html): StreamInterface
    {
        $dompdf = $this->buildDompdf();
        $dompdf->loadHtml($html);

        return $this->createStream($dompdf);
    }

    private function buildDompdf(): Dompdf\Dompdf
    {
        return new Dompdf\Dompdf( $this->compileConstructOptions());
    }

    private function compileConstructOptions(): Dompdf\Options
    {
        $options = new Dompdf\Options(
            is_array($this->options->extraOptions['construct'])
                ? $this->options->extraOptions['construct']
                : null
        );

        if (null !== $this->options->pageOrientation) {
            $options->setDefaultPaperOrientation(
                $this->options->pageOrientation->value
            );
        }

        return $options;
    }

    /**
     * @return array<mixed, mixed>
     */
    private function compileOutputOptions(): array
    {
        $options = $this->options->extraOptions['output'];

        if (false === is_array($options)) {
            $options = [];
        }

        return $options;
    }

    private function createStream(Dompdf\Dompdf $dompdf): StreamInterface
    {
        $output = $dompdf->output($this->compileOutputOptions());

        return $this
            ->streamFactory
            ->createStream($output ?: '')
        ;
    }
}
