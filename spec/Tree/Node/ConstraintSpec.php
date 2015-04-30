<?php

namespace spec\Ckr\Fiql\Tree\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConstraintSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith('field', '=gt=', 'argument');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Tree\Node\Constraint');
    }

    function it_returns_the_field()
    {
        $this->getField()->shouldReturn('field');
    }

    function it_returns_the_operator()
    {
        $this->getOperator()->shouldReturn('=gt=');
    }

    function it_returns_the_argument()
    {
        $this->getArgument()->shouldReturn('argument');
    }
}
