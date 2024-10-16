<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Tests\Stream;

use KNPLabs\Snappy\Core\Stream\FileStream;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

final class FileStreamTest extends TestCase
{
    private FileStream $stream;

    public function setUp(): void
    {
        $this->stream = FileStream::createTmpFile(
            new Psr17Factory,
        );
    }

    public function testTmpFileStreamCreateTemporaryFile(): void
    {
        $file = $this->stream->file;

        $this->assertFileExists($file->getPathname());
        $this->assertFileIsReadable( $file->getPathname());
        $this->assertFileIsWritable( $file->getPathname());
    }

    public function testTmpFileStreamReadTheFile(): void
    {
        $file = $this->stream->file;

        file_put_contents($file->getPathname(), 'the content');

        $this->assertEquals(
            (string) $this->stream,
            'the content',
        );
    }

    public function testTmpFileIsAutomaticalyRemoved(): void
    {
        $file = $this->stream->file;

        $this->assertFileExists($file->getPathname());

        unset($this->stream);

        $this->assertFileDoesNotExist($file->getPathname());
    }
}
