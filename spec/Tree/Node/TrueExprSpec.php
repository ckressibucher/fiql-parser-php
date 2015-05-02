<?php

namespace spec\Ckr\Fiql\Tree\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TrueExprSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Tree\Node\TrueExpr');
    }

    function it_should_implement_the_node_interface()
    {
        $this->shouldHaveType('Ckr\Fiql\Tree\Node');
    }

    function its_getType_should_return_the_string_true()
    {
        $this->getType()->shouldReturn('true');
    }
}
