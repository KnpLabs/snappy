<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Core\Tests\Filesystem;

use KNPLabs\Snappy\Core\Filesystem\SplResourceInfo;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class SplResourceInfoTest extends TestCase
{
    public function testCanBuildFromTmpFile(): void
    {
        $file = SplResourceInfo::fromTmpFile();

        $path = $file->getPathname();

        self::assertFileExists($path);

        unset($file);

        self::assertFileDoesNotExist($path);
    }
}
