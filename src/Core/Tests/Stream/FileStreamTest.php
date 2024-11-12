<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Tests\Stream;

use KNPLabs\Snappy\Core\Stream\FileStream;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class FileStreamTest extends TestCase
{
    private ?FileStream $stream = null;

    protected function setUp(): void
    {
        $this->stream = FileStream::createTmpFile(
            new Psr17Factory(),
        );
    }

    public function testTmpFileStreamCreateTemporaryFile(): void
    {
        self::assertNotNull($this->stream);

        $file = $this->stream->file;

        self::assertFileExists($file->getPathname());
        self::assertFileIsReadable($file->getPathname());
        self::assertFileIsWritable($file->getPathname());
    }

    public function testTmpFileStreamReadTheFile(): void
    {
        self::assertNotNull($this->stream);

        $file = $this->stream->file;

        file_put_contents($file->getPathname(), 'the content');

        self::assertSame(
            (string) $this->stream,
            'the content',
        );
    }

    public function testTmpFileIsAutomaticalyRemoved(): void
    {
        self::assertNotNull($this->stream);

        $file = $this->stream->file;

        self::assertFileExists($file->getPathname());

        $this->stream = null;

        self::assertFileDoesNotExist($file->getPathname());
    }
}
