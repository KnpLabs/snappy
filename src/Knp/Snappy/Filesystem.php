<?php

declare(strict_types=1);

namespace Knp\Snappy;

use Knp\Snappy\Exception\FileNotFound;

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
     * @return string
     *
     * @throws FileNotFound
     */
    public function getFileContents(string $path): string
    {
        if (!$this->exists($path)) {
            throw FileNotFound::notFound($path);
        }

        return file_get_contents($path);
    }

    /**
     * Get the size of the file at $path.
     *
     * @param string $path
     *
     * @return int Size of the file in bytes
     *
     * @throws FileNotFound
     */
    public function getFileSize(string $path): int
    {
        if (!$this->exists($path)) {
            throw FileNotFound::notFound($path);
        }

        return filesize($path);
    }

    /**
     * Deletes a file.
     *
     * @param string $filename
     */
    public function unlink(string $filename)
    {
        if (!$this->exists($filename)) {
            return;
        }

        unlink($filename);

        if ($this->exists($filename)) {
            throw new \RuntimeException(sprintf('Could not delete file "%s".', $filename));
        }
    }

    /**
     * Creates a directory.
     *
     * @param string $pathname
     */
    public function mkdir(string $pathname)
    {
        @mkdir($pathname, 0777, true);

        if (!$this->isDir($pathname)) {
            throw new \RuntimeException(sprintf('Unable to create directory "%s".', $pathname));
        }
    }

    /**
     * @param string|null $content
     * @param string|null $extension
     *
     * @return string The filename of the temporary file
     */
    public function createTemporaryFile(string $content = null, string $extension = null): string
    {
        $dir = rtrim($this->temporaryDirectory, DIRECTORY_SEPARATOR);

        if (!is_dir($dir)) {
            $this->mkdir($dir);
        } elseif (!is_writable($dir)) {
            throw new \RuntimeException(sprintf("Unable to write in directory: %s\n", $dir));
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
}
