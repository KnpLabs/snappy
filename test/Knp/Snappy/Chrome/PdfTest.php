<?php

declare(strict_types=1);

namespace Knp\Snappy\Chrome;

use Knp\Snappy\Filesystem;
use PHPUnit\Framework\TestCase;

class PdfTest extends TestCase
{
    public function testOptionManipulation()
    {
        $backend = $this->createMock(Backend::class);
        $filesystem = $this->createMock(Filesystem::class);

        $backend->expects($this->once())
            ->method('run')
            ->with($this->anything(), [
                'incognito' => true, 'hide-scrollbars' => true, 'disable-gpu' => true, 'enable-viewport' => true,
                'headless' => true, 'print-to-pdf' => '/tmp/output.pdf'
            ]);

        $pdf = new Pdf($backend, []);
        $pdf->setFilesystem($filesystem);
        $pdf->enableOption('incognito');
        $pdf->setOption('hide-scrollbars', true);
        $pdf->setOptions(['disable-gpu' => true, 'enable-viewport' => true, 'homedir' => '/tmp']);
        $pdf->removeOption('homedir');

        $this->assertNull($pdf->generate('/tmp/input.html', '/tmp/output.pdf'));
    }

    public function testGenerate()
    {
        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['prepareOutput'])
            ->getMock();
        $filesystem->expects($this->once())
            ->method('prepareOutput')
            ->with('/tmp/output.pdf', true);

        $backend = $this->getMockBuilder(Backend::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();
        $backend->expects($this->once())
            ->method('run')
            ->with(
                'file:///input.html',
                ['enable-viewport' => true, 'disable-gpu' => true, 'headless' => true, 'print-to-pdf' => '/tmp/output.pdf']
            );

        $pdf = new Pdf($backend, ['enable-viewport' => true]);
        $pdf->setFilesystem($filesystem);

        $pdf->generate('/input.html', '/tmp/output.pdf', ['disable-gpu' => true], true);
    }

    public function testGenerateFromHtml()
    {
        $html = '<html></html>';

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['prepareOutput'])
            ->getMock();
        $filesystem->expects($this->once())
            ->method('prepareOutput')
            ->with('/tmp/output.pdf', true);

        $backend = $this->getMockBuilder(Backend::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();
        $backend->expects($this->once())
            ->method('run')
            ->with(
                'data:text/html,' . rawurlencode($html),
                ['enable-viewport' => true, 'disable-gpu' => true, 'headless' => true, 'print-to-pdf' => '/tmp/output.pdf']
            );

        $pdf = new Pdf($backend, ['enable-viewport' => true]);
        $pdf->setFilesystem($filesystem);

        $pdf->generateFromHtml($html, '/tmp/output.pdf', ['disable-gpu' => true], true);
    }

    public function testGetOutput()
    {
        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['createTemporaryFile', 'getFileContents'])
            ->getMock();
        $filesystem->expects($this->once())
            ->method('createTemporaryFile')
            ->with(null, 'pdf')
            ->willReturn('/tmp/output.pdf');
        $filesystem->expects($this->once())
            ->method('getFileContents')
            ->with('/tmp/output.pdf')
            ->willReturn('pdf file content');

        $backend = $this->getMockBuilder(Backend::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();
        $backend->expects($this->once())
            ->method('run')
            ->with(
                'file:///tmp/input.html',
                ['enable-viewport' => true, 'disable-gpu' => true, 'headless' => true, 'print-to-pdf' => '/tmp/output.pdf']
            );

        $pdf = new Pdf($backend, ['enable-viewport' => true]);
        $pdf->setFilesystem($filesystem);

        $this->assertSame('pdf file content', $pdf->getOutput('/tmp/input.html', ['disable-gpu' => true]));
    }

    public function testGetOutputFromHtml()
    {
        $html = '<html></html>';

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['createTemporaryFile', 'getFileContents'])
            ->getMock();
        $filesystem->expects($this->once())
            ->method('createTemporaryFile')
            ->willReturn('/tmp/output.pdf');
        $filesystem->expects($this->once())
            ->method('getFileContents')
            ->willReturn('pdf file content');

        $backend = $this->getMockBuilder(Backend::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();
        $backend->expects($this->once())
            ->method('run')
            ->with(
                'data:text/html,' . rawurlencode($html),
                ['enable-viewport' => true, 'disable-gpu' => true, 'headless' => true, 'print-to-pdf' => '/tmp/output.pdf']
            );

        $pdf = new Pdf($backend, ['enable-viewport' => true]);
        $pdf->setFilesystem($filesystem);

        $this->assertSame('pdf file content', $pdf->getOutputFromHtml($html, ['disable-gpu' => true]));
    }
}
