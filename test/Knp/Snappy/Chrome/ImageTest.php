<?php

declare(strict_types=1);

namespace Knp\Snappy\Chrome;

use Knp\Snappy\Filesystem;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testOptionManipulation()
    {
        $backend = $this->createMock(Backend::class);
        $filesystem = $this->createMock(Filesystem::class);

        $backend->expects($this->once())
            ->method('run')
            ->with($this->anything(), [
                'incognito' => true, 'hide-scrollbars' => true, 'disable-gpu' => true, 'enable-viewport' => true,
                'headless'  => true, 'screenshot' => '/tmp/output.jpg',
            ]);

        $image = new Image($backend, []);
        $image->setFilesystem($filesystem);
        $image->enableOption('incognito');
        $image->setOption('hide-scrollbars', true);
        $image->setOptions(['disable-gpu' => true, 'enable-viewport' => true, 'homedir' => '/tmp']);
        $image->removeOption('homedir');

        $this->assertNull($image->generate('/tmp/input.html', '/tmp/output.jpg'));
    }

    public function testGenerate()
    {
        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['prepareOutput'])
            ->getMock();
        $filesystem->expects($this->once())
            ->method('prepareOutput')
            ->with('/tmp/output.jpg', true);

        $backend = $this->getMockBuilder(Backend::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();
        $backend->expects($this->once())
            ->method('run')
            ->with(
                'file:///input.html',
                ['enable-viewport' => true, 'disable-gpu' => true, 'headless' => true, 'screenshot' => '/tmp/output.jpg']
            );

        $image = new Image($backend, ['enable-viewport' => true]);
        $image->setFilesystem($filesystem);

        $image->generate('/input.html', '/tmp/output.jpg', ['disable-gpu' => true], true);
    }

    public function testGenerateFromHtml()
    {
        $html = '<html></html>';

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['prepareOutput'])
            ->getMock();
        $filesystem->expects($this->once())
            ->method('prepareOutput')
            ->with('/tmp/output.jpg', true);

        $backend = $this->getMockBuilder(Backend::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();
        $backend->expects($this->once())
            ->method('run')
            ->with(
                'data:text/html,' . rawurlencode($html),
                ['enable-viewport' => true, 'disable-gpu' => true, 'headless' => true, 'screenshot' => '/tmp/output.jpg']
            );

        $image = new Image($backend, ['enable-viewport' => true]);
        $image->setFilesystem($filesystem);

        $image->generateFromHtml($html, '/tmp/output.jpg', ['disable-gpu' => true], true);
    }

    public function testGetOutput()
    {
        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['createTemporaryFile', 'getFileContents'])
            ->getMock();
        $filesystem->expects($this->once())
            ->method('createTemporaryFile')
            ->with(null, 'jpg')
            ->willReturn('/tmp/output.jpg');
        $filesystem->expects($this->once())
            ->method('getFileContents')
            ->with('/tmp/output.jpg')
            ->willReturn('image content');

        $backend = $this->getMockBuilder(Backend::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();
        $backend->expects($this->once())
            ->method('run')
            ->with(
                'file:///tmp/input.html',
                ['enable-viewport' => true, 'disable-gpu' => true, 'headless' => true, 'screenshot' => '/tmp/output.jpg']
            );

        $image = new Image($backend, ['enable-viewport' => true]);
        $image->setFilesystem($filesystem);

        $this->assertSame('image content', $image->getOutput('/tmp/input.html', ['disable-gpu' => true]));
    }

    public function testGetOutputFromHtml()
    {
        $html = '<html></html>';

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['createTemporaryFile', 'getFileContents'])
            ->getMock();
        $filesystem->expects($this->once())
            ->method('createTemporaryFile')
            ->willReturn('/tmp/output.jpg');
        $filesystem->expects($this->once())
            ->method('getFileContents')
            ->willReturn('image content');

        $backend = $this->getMockBuilder(Backend::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();
        $backend->expects($this->once())
            ->method('run')
            ->with(
                'data:text/html,' . rawurlencode($html),
                ['enable-viewport' => true, 'disable-gpu' => true, 'headless' => true, 'screenshot' => '/tmp/output.jpg']
            );

        $image = new Image($backend, ['enable-viewport' => true]);
        $image->setFilesystem($filesystem);

        $this->assertSame('image content', $image->getOutputFromHtml($html, ['disable-gpu' => true]));
    }
}
