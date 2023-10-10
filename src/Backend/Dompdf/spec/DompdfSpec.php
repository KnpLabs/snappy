<?php

namespace spec\KnpLabs\Snappy\Backend\Dompdf;

use Dompdf\Dompdf as DompdfLib;
use KnpLabs\Snappy\Backend\Dompdf\Dompdf;
use PhpSpec\ObjectBehavior;

class DompdfSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Dompdf::class);
    }

    function it_generate_from_string(DompdfLib $dompdf)
    {
        $dompdf->output()->willReturn('<--PDF string-->');
        $this->generateFromString('<html />')->shouldReturn('<--PDF string-->');
    }
}
