<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\Dompdf;

use Dompdf;
use KNPLabs\Snappy\Core\Backend\Adapter\DOMDocumentToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlFileToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\HtmlToPdf;
use KNPLabs\Snappy\Core\Backend\Adapter\Reconfigurable;
use KNPLabs\Snappy\Core\Backend\Options;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final readonly class DompdfAdapter implements DOMDocumentToPdf, HtmlFileToPdf, HtmlToPdf
{
    /**
     * @use Reconfigurable<self>
     */
    use Reconfigurable;

    public function __construct(DompdfFactory $factory, Options $options, private StreamFactoryInterface $streamFactory)
    {
        $this->factory = $factory;
        $this->options = $options;
    }

    public function generateFromDOMDocument(\DOMDocument $document): StreamInterface
    {
        $dompdf = $this->buildDompdf();
        $dompdf->loadDOM($document);

        return $this->createStream($dompdf);
    }

    public function generateFromHtmlFile(\SplFileInfo $file): StreamInterface
    {
        $dompdf = $this->buildDompdf();
        $dompdf->loadHtmlFile($file->getPathname());

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
        return new Dompdf\Dompdf($this->compileConstructOptions());
    }

    private function compileConstructOptions(): Dompdf\Options
    {
        $options = new Dompdf\Options(
            \is_array($this->options->extraOptions['construct'] ?? null)
                ? $this->options->extraOptions['construct']
                : null
        );

        if (null !== $this->options->pageOrientation) {
            $options->setDefaultPaperOrientation(
                strtolower(
                    $this->options->pageOrientation->name
                )
            );
        }

        return $options;
    }

    /**
     * @return array<mixed, mixed>
     */
    private function compileOutputOptions(): array
    {
        $options = $this->options->extraOptions['output'] ?? null;

        if (false === \is_array($options)) {
            $options = [];
        }

        return $options;
    }

    private function createStream(Dompdf\Dompdf $dompdf): StreamInterface
    {
        $dompdf->render();
        $output = $dompdf->output($this->compileOutputOptions());

        return $this
            ->streamFactory
            ->createStream($output ?: '')
        ;
    }
}
