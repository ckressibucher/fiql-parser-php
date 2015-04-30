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

    function let()
    {
        $scanner = new Scanner();
        $this->beConstructedWith($scanner);
    }


    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Parser');
    }

    function it_creates_a_matcher_expression()
    {
        $expr = 'field';
        $expected = new Matcher('field');
        $this->parse($expr)->shouldBeEqualToTree($expected);
    }

    function it_creates_a_constraint_expression()
    {
        $expr = 'my%20field=lt=the%20value';
        $expected = new Constraint('my field', '=lt=', 'the value');
        $this->parse($expr)->shouldBeEqualToTree($expected);
    }

    function it_handles_grouped_expressions()
    {
        $expr = '((my_field))';
        $expected = new Matcher('my_field');
        $this->parse($expr)->shouldBeEqualToTree($expected);
    }

    function it_checks_correct_number_of_grouping_parenthesis()
    {
        $expr = '((my_field)';
        $this->shouldThrow('Ckr\Fiql\Parser\ParseException')->during('parse', array($expr));
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
