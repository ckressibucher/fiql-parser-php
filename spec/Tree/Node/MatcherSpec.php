<?php

namespace spec\Ckr\Fiql\Tree\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Tree\Node\Matcher');
    }
}