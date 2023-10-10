<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\Backend\Dompdf;

use ArrayAccess;
use Dompdf\Dompdf as DompdfLib;
use KnpLabs\Snappy\Core\FileToPdf;
use KnpLabs\Snappy\Core\StringToPdf;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

final class Dompdf implements FileToPdf, StringToPdf
{
    public function __construct(private StreamFactoryInterface $streamFactory)
    {
    }

    public function generateFromFile(SplFileInfo $file, ArrayAccess|array $options = []): StreamInterface
    {
        $dompdf = new DompdfLib();
        $dompdf->loadHtmlFile($file->getPath());
        $dompdf->render();
        return $this->streamFactory->createStream($dompdf->output());
    }

    public function generateFromString(string $html, ArrayAccess|array $options = []): StreamInterface
    {
        $dompdf = new DompdfLib();
        $dompdf->loadHtml($html);
        $dompdf->render();
        return $this->streamFactory->createStream($dompdf->output());
    }
}
