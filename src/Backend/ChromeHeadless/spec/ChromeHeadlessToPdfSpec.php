<?php

namespace spec\KnpLabs\Snappy\Backend\ChromeHeadless;

use Assert\Assert;
use KnpLabs\Snappy\Backend\ChromeHeadless\ChromeHeadlessToPdf;
use Nyholm\Psr7\Factory\Psr17Factory;
use PhpSpec\ObjectBehavior;

class ChromeHeadlessToPdfSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(
            '/usr/bin/chromium',
            new Psr17Factory()
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ChromeHeadlessToPdf::class);
    }

    public function it_generates_a_pdf_from_a_file()
    {
        $file = new \SplFileInfo(__DIR__.'/ChromeHeadlessToPdf/example.html');

        $stream = $this->generateFromFile($file, [
            'headless' => null,
            'disable-gpu' => null,
        ]);

        $info = new \finfo(FILEINFO_MIME);

        $mime = $info->buffer($stream->getWrappedObject());

        Assert::that($mime)->eq('application/pdf; charset=binary');
    }

    public function it_generates_a_pdf_from_an_url()
    {
        $factory = new Psr17Factory();

        $uri = $factory->createUri('https://gist.githubusercontent.com/Swanoo/0b5a6987f6032c31b30975ca78c47f2d/raw/3aee4bcb22b6d0c37c58f335b1befe8ee2b830ec/example.html');

        $stream = $this->generateFromUri($uri, [
            'headless' => null,
            'disable-gpu' => null,
        ]);

        $info = new \finfo(FILEINFO_MIME);

        $mime = $info->buffer($stream->getWrappedObject());

        Assert::that($mime)->eq('application/pdf; charset=binary');
    }
}
