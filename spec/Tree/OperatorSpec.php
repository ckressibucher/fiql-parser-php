<?php

namespace spec\Ckr\Fiql\Tree;

use Ckr\Fiql\Tree\Operator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OperatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(1);
        $this->shouldHaveType('Ckr\Fiql\Tree\Operator');
    }

    function it_can_set_a_type_during_construction_and_get_it_later()
    {
        $this->beConstructedWith(2);
        $this->getType()->shouldBeEqualTo(2);
    }

    function it_can_be_rendered_to_a_string()
    {
        $this->beConstructedWith(Operator::COMPARE_GREATER_EQUAL);
        $this->__toString()->shouldBeEqualTo('>=');
    }
}
