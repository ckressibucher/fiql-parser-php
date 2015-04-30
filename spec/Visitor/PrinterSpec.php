<?php

namespace spec\Ckr\Fiql\Visitor;

use Ckr\Fiql\Tree\Node\Constraint;
use Ckr\Fiql\Tree\Node\Matcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PrinterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Visitor\Printer');
    }

    function it_should_implement_visitor()
    {
        $this->shouldHaveType('Ckr\Fiql\Tree\Visitor');
    }

    function it_should_return_type_and_selector_of_a_matcher_node()
    {
        $matcher = new Matcher('field');
        $this->visit($matcher);

        $this->getText()->shouldReturn('matcher:field');
    }

    function it_should_return_type_and_expression_of_a_constraint_node()
    {
        $constraint = new Constraint('field', '=lt=', 'value');
        $this->visit($constraint);

        $this->getText()->shouldReturn('constraint:field=lt=value');
    }

    function it_should_be_resettable()
    {
        $matcher = new Matcher('a');
        $this->visit($matcher);
        $this->reset();
        $this->getText()->shouldReturn('');
    }
}
