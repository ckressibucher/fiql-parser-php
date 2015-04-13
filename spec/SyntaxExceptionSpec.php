<?php

namespace spec\Ckr\Fiql;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SyntaxExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\SyntaxException');
    }
}
