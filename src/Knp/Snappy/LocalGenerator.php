<?php

namespace Knp\Snappy;

/**
 * Represents a Generator that needs access to local filesystem in order to perform generation tasks.
 *
 * @author Albin Kerouanton <albin.kerouanton@knplabs.com>
 */
interface LocalGenerator extends Generator
{
    /**
     * Sets the filesystem instance used to manage local files and directories.
     *
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem);
}
