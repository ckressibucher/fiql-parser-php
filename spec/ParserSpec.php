<?php

namespace spec\Ckr\Fiql;

use Ckr\Fiql\Scanner;
use Ckr\Fiql\Tree\Node\Constraint;
use Ckr\Fiql\Tree\Node\Matcher;
use Ckr\Fiql\Visitor\Printer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Parser');
    }

    function it_creates_a_matcher_expression()
    {
        $expr = 'field';
        $scanner = new Scanner();
        $expected = new Matcher('field');
        $this->parse($scanner, $expr)->shouldBeEqualToTree($expected);
    }

    public function getMatchers()
    {
        return [
            'beEqualToTree' => function($subject, $expected) {
                if ($subject->getType() !== $expected->getType()) {
                    return false;
                }

                $printer = new Printer();
                $subject->accept($printer);
                $actualText = $printer->getText();

                $printer->reset();
                $expected->accept($printer);
                $expectedText = $printer->getText();
                
                return $actualText === $expectedText;
            }
        ];
    }
}
