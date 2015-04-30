<?php

namespace spec\Ckr\Fiql\Parser;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParseExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Parser\ParseException');
    }

    function it_is_of_type_exception()
    {
        $this->shouldHaveType('Exception');
    }
}
