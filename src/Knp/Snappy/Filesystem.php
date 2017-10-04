<?php

declare(strict_types=1);

namespace Knp\Snappy;

use Knp\Snappy\Filesystem\Exception\CouldNotCreateDirectory;
use Knp\Snappy\Filesystem\Exception\CouldNotDeleteFile;
use Knp\Snappy\Filesystem\Exception\DirectoryNotWritable;
use Knp\Snappy\Filesystem\Exception\FileAlreadyExistsException;
use Knp\Snappy\Filesystem\Exception\FileNotFound;

class Filesystem
{
    /** @var string */
    private $temporaryDirectory;

    /** @var string[] */
    private $temporaryFiles = [];

    /**
     * @param string|null $temporaryDirectory Directory used to store temporary files.
     */
    public function __construct(string $temporaryDirectory = null)
    {
        $this->temporaryDirectory = $temporaryDirectory ?: sys_get_temp_dir();

        register_shutdown_function([$this, 'removeTemporaryFiles']);
    }

    public function __destruct()
    {
        $this->removeTemporaryFiles();
    }

    /**
     * Removes all temporary files.
     */
    public function removeTemporaryFiles()
    {
        foreach ($this->temporaryFiles as $file) {
            if (!$this->exists($file)) {
                continue;
            }

            $this->unlink($file);
        }
    }

    /**
     * Checks if $path exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Check if $path is a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * Check if $path is a directory.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isDir(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Get the content of the file at $path.
     *
     * @param string $path
     *
     * @throws FileNotFound
     *
     * @return string
     */
    public function getFileContents(string $path): string
    {
        if (!$this->exists($path)) {
            throw FileNotFound::file($path);
        }

        return file_get_contents($path);
    }

    /**
     * Get the size of the file at $path.
     *
     * @param string $path
     *
     * @throws FileNotFound
     *
     * @return int Size of the file in bytes
     */
    public function getFileSize(string $path): int
    {
        if (!$this->exists($path)) {
            throw FileNotFound::file($path);
        }

        return filesize($path);
    }

    /**
     * Deletes a file.
     *
     * @param string $filename
     *
     * @throws CouldNotDeleteFile If the file $filename exist but could not be deleted
     */
    public function unlink(string $filename)
    {
        if (!$this->exists($filename)) {
            return;
        }

        unlink($filename);

        if ($this->exists($filename)) {
            throw CouldNotDeleteFile::file($filename);
        }
    }

    /**
     * Creates a directory.
     *
     * @param string $pathname
     *
     * @throws CouldNotCreateDirectory If the directory could not be created
     */
    public function mkdir(string $pathname)
    {
        @mkdir($pathname, 0777, true);

        if (!$this->isDir($pathname)) {
            throw CouldNotCreateDirectory::directory($pathname);
        }
    }

    /**
     * @param string|null $content
     * @param string|null $extension
     *
     * @throws DirectoryNotWritable    If the temporary directory is not writable
     * @throws CouldNotCreateDirectory If the temporary directory does not exist and it could not be created
     *
     * @return string The filename of the temporary file
     */
    public function createTemporaryFile(string $content = null, string $extension = null): string
    {
        $dir = rtrim($this->temporaryDirectory, DIRECTORY_SEPARATOR);

        if (!is_dir($dir)) {
            $this->mkdir($dir);
        } elseif (!is_writable($dir)) {
            throw DirectoryNotWritable::directory($dir);
        }

        $filename = $dir . DIRECTORY_SEPARATOR . uniqid('knp_snappy', true);

        if (null !== $extension) {
            $filename .= '.' . $extension;
        }

        if (null !== $content) {
            file_put_contents($filename, $content);
        }

        $this->temporaryFiles[] = $filename;

        return $filename;
    }

    /**
     * Prepares the specified output.
     *
     * @param string $filename  The output filename
     * @param bool   $overwrite Whether to overwrite the file if it already
     *                          exist
     *
     * @throws \Knp\Snappy\Filesystem\Exception\FileAlreadyExistsException If the file already exists and should not be overwritten.
     * @throws Filesystem\Exception\CouldNotDeleteFile                     If the file should be overwritten but could not be deleted.
     * @throws Filesystem\Exception\CouldNotCreateDirectory                If the parent directory does not exist and could not be created.
     */
    public function prepareOutput(string $filename, bool $overwrite)
    {
        $directory = dirname($filename);

        if ($this->exists($filename)) {
            if (!$this->isFile($filename)) {
                throw new \InvalidArgumentException(sprintf(
                    'The output file \'%s\' already exists and it is a %s.',
                    $filename, $this->isDir($filename) ? 'directory' : 'link'
                ));
            } elseif (false === $overwrite) {
                throw new FileAlreadyExistsException(sprintf(
                    'The output file \'%s\' already exists.',
                    $filename
                ));
            }

            $this->unlink($filename);
        } elseif (!$this->isDir($directory)) {
            $this->mkdir($directory);
        }
    }
}
