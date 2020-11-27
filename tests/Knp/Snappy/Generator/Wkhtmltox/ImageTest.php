<?php

namespace Tests\Knp\Snappy\Generator\Wkhtmltox;

use Knp\Snappy\Generator\Wkhtmltox\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testCreateInstance(): void
    {
        $testObject = new Image();
        $this->assertInstanceOf(Image::class, $testObject);
    }
}
