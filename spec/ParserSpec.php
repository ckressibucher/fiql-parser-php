<?php

namespace spec\Ckr\Fiql;

use Ckr\Fiql\Scanner;
use Ckr\Fiql\Tree\Node\Constraint;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Parser');
    }

//    function it_creates_a_constraint_expression()
//    {
//        $expr = 'field==val';
//        $scanner = new Scanner($expr);
//
//        $expected = new Constraint();
//
//        $this->parse($scanner)->should
//    }
//
//    public function getMatchers()
//    {
//        return [
//            'compareTreeTo' => function($expected, $actual) {}
//        ];
//    }
}
