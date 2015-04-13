<?php

namespace spec\Ckr\Fiql;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Parser');
    }
}
