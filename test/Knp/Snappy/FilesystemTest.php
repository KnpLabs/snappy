<?php

declare(strict_types=1);

namespace Knp\Snappy;

use Knp\Snappy\Exception\FileNotFound;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    /** @var string */
    private $directory;

    /** @var Filesystem */
    private $filesystem;

    public function setUp()
    {
        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('knp_snappy');
        mkdir($this->directory, 0777, true);

        $this->filesystem = new Filesystem();
    }

    public function tearDown()
    {
        if (file_exists($this->directory)) {
            $iterator = new \RecursiveDirectoryIterator(
                $this->directory,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS
            );

            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    unlink(strval($item));
                } elseif ($item->isDir()) {
                    rmdir(strval($item));
                }
            }

            rmdir($this->directory);
        }
    }

    public function testExists()
    {
        touch($path = $this->directory . DIRECTORY_SEPARATOR . 'exists');
        $this->assertTrue($this->filesystem->exists($path));
        $this->assertFalse($this->filesystem->exists('/does-not-exist'));
    }

    public function testIsFile()
    {
        touch($path = $this->directory . DIRECTORY_SEPARATOR . 'is-file');
        $this->assertTrue($this->filesystem->isFile($path));

        mkdir($path = $this->directory . DIRECTORY_SEPARATOR . 'is-not-a-file');
        $this->assertFalse($this->filesystem->isFile($path));

        $this->assertFalse($this->filesystem->isFile('/does-not-exist'));
    }

    public function testIsDir()
    {
        mkdir($path = $this->directory . DIRECTORY_SEPARATOR . 'is-dir');
        $this->assertTrue($this->filesystem->isDir($path));

        touch($path = $this->directory . DIRECTORY_SEPARATOR . 'is-not-a-dir');
        $this->assertFalse($this->filesystem->isDir($path));

        $this->assertFalse($this->filesystem->isFile('/does-not-exist'));
    }

    public function testUnlink()
    {
        touch($path = $this->directory . DIRECTORY_SEPARATOR . 'unlink');
        $this->filesystem->unlink($path);
        $this->assertFalse($this->filesystem->exists($path));
    }

    public function testUnlinkDoesNotThrowAnErrorIfUnlinkedFileDoesNotExist()
    {
        $this->assertNull($this->filesystem->unlink('/does-not-exist'));
    }

    public function testMkdir()
    {
        $this->filesystem->mkdir($path = $this->directory . DIRECTORY_SEPARATOR . 'mkdir');
        $this->assertTrue($this->filesystem->isDir($path));
    }

    public function testMkdirDoesNotThrowAnErrorIfPathAlreadyExist()
    {
        $this->filesystem->mkdir($path = $this->directory . DIRECTORY_SEPARATOR . 'mkdir');
        $this->assertNull($this->filesystem->mkdir($path));
    }

    public function testItCreatesTheTemporaryDirectoryWhenItDoesNotExist()
    {
        $filesystem = new Filesystem($path = $this->directory . DIRECTORY_SEPARATOR . 'create-temporary-directory');
        $this->assertFalse(is_dir($path));

        $filesystem->createTemporaryFile();
        $this->assertTrue(is_dir($path));
    }

    public function testGetFileContentsThrowsExceptionWhenFileDoesNoExist()
    {
        $this->expectException(FileNotFound::class);

        $this->filesystem->getFileContents('/get-file-contents');
    }

    public function testGetFileContents()
    {
        file_put_contents($path = $this->directory . '/get-file-contents', 'foo');

        $this->assertSame('foo', $this->filesystem->getFileContents($path));
    }

    public function testGetFileSizeThrowsExceptionWhenFileDoesNoExist()
    {
        $this->expectException(FileNotFound::class);

        $this->filesystem->getFileSize('/get-file-contents');
    }

    public function testGetFileSize()
    {
        file_put_contents($path = $this->directory . '/get-file-contents', 'foo');

        $this->assertSame(3, $this->filesystem->getFileSize($path));
    }
}
