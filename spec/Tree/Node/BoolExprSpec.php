<?php

namespace spec\Ckr\Fiql\Tree\Node;

use Ckr\Fiql\Tree\Node;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BoolExprSpec extends ObjectBehavior
{

    function it_is_initializable(Node $left, Node $right)
    {
        $this->beConstructedWith($left, Node\BoolExpr::OP_AND, $right);
        $this->shouldHaveType('Ckr\Fiql\Tree\Node\BoolExpr');
    }

    function it_implements_the_node_interface(Node $left, Node $right)
    {
        $this->beConstructedWith($left, Node\BoolExpr::OP_AND, $right);
        $this->shouldHaveType('Ckr\Fiql\Tree\Node');
    }

    function its_getLeftOperand_returns_the_left_node(Node $left, Node $right)
    {
        $this->beConstructedWith($left, Node\BoolExpr::OP_AND, $right);
        $this->getLeftOperand()->shouldReturn($left);
    }

    function its_getRightOperand_returns_the_left_node(Node $left, Node $right)
    {
        $this->beConstructedWith($left, Node\BoolExpr::OP_OR, $right);
        $this->getRightOperand()->shouldReturn($right);
    }

    function its_getOperator_returns_the_operator(Node $left, Node $right)
    {
        $this->beConstructedWith($left, Node\BoolExpr::OP_OR, $right);
        $this->getOperator()->shouldReturn(Node\BoolExpr::OP_OR);
    }
}
