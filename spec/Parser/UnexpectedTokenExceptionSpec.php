<?php

namespace spec\Ckr\Fiql\Parser;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UnexpectedTokenExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Parser\UnexpectedTokenException');
    }

    function it_is_of_type_parse_exception()
    {
        $this->shouldHaveType('Ckr\Fiql\Parser\ParseException');
    }
}
