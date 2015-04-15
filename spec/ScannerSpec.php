<?php

namespace spec\Ckr\Fiql;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ckr\Fiql\Scanner;

class ScannerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Scanner');
    }

    function it_returns_an_array_of_tokens()
    {
        $this->scan('selector')->shouldBeEqualTo([
            [Scanner::T_SELECTOR, 'selector']
        ]);
    }

    function it_recognizes_a_selector()
    {
        /* @see https://tools.ietf.org/html/rfc3986#section-2.3 */
        $exampleSelector = 'lettersALPHA456-._~';
        $this->scan($exampleSelector)->shouldBeEqualTo([
            [Scanner::T_SELECTOR, $exampleSelector]
        ]);
    }

    function it_recognizes_a_constraint()
    {
        $exampleConstraint = 'a==b';
        $this->scan($exampleConstraint)->shouldBeEqualTo([
            [Scanner::T_SELECTOR, 'a'],
            [Scanner::T_COMP_OPERATOR, '=='],
            [Scanner::T_ARGUMENT, 'b']
        ]);
    }

    function it_recognizes_a_group()
    {
        $exampleGroup = '(selector)';
        $this->scan($exampleGroup)->shouldBeEqualTo([
            [Scanner::T_GROUP_START, '('],
            [Scanner::T_SELECTOR, 'selector'],
            [Scanner::T_GROUP_END, ')'],
        ]);
    }

    function it_recognizes_a_double_group()
    {
        $source = '((select))';
        $this->scan($source)->shouldBeEqualTo([
            [Scanner::T_GROUP_START, '('],
            [Scanner::T_GROUP_START, '('],
            [Scanner::T_SELECTOR, 'select'],
            [Scanner::T_GROUP_END, ')'],
            [Scanner::T_GROUP_END, ')'],
        ]);
    }

    function it_recognizes_an_and_operator()
    {
        $example = 'a;b';
        $this->scan($example)->shouldBeEqualTo([
            [Scanner::T_SELECTOR, 'a'],
            [Scanner::T_BOOL_OPERATOR, ';'],
            [Scanner::T_SELECTOR, 'b']
        ]);
    }

    function it_recognizes_an_or_operator()
    {
        $example = 'a,b';
        $this->scan($example)->shouldBeEqualTo([
            [Scanner::T_SELECTOR, 'a'],
            [Scanner::T_BOOL_OPERATOR, ','],
            [Scanner::T_SELECTOR, 'b']
        ]);
    }


    function it_allows_multiple_bool_operators_without_parentheses()
    {
        $source = 'a;b;c==5';
        $this->scan($source)->shouldBeEqualTo([
            [Scanner::T_SELECTOR, 'a'],
            [Scanner::T_BOOL_OPERATOR, ';'],
            [Scanner::T_SELECTOR, 'b'],
            [Scanner::T_BOOL_OPERATOR, ';'],
            [Scanner::T_SELECTOR, 'c'],
            [Scanner::T_COMP_OPERATOR, '=='],
            [Scanner::T_ARGUMENT, '5'],
        ]);
    }

    function it_tokenizes_a_complex_example()
    {
        $example = 'abc==5;(xyz=gt=3,field!=value;(checkFlag))';
        $this->scan($example)->shouldBeEqualTo([
            [Scanner::T_SELECTOR, 'abc'],
            [Scanner::T_COMP_OPERATOR, '=='],
            [Scanner::T_ARGUMENT, '5'],
            [Scanner::T_BOOL_OPERATOR, ';'],
            [Scanner::T_GROUP_START, '('],
            [Scanner::T_SELECTOR, 'xyz'],
            [Scanner::T_COMP_OPERATOR, '=gt='],
            [Scanner::T_ARGUMENT, '3'],
            [Scanner::T_BOOL_OPERATOR, ','],
            [Scanner::T_SELECTOR, 'field'],
            [Scanner::T_COMP_OPERATOR, '!='],
            [Scanner::T_ARGUMENT, 'value'],
            [Scanner::T_BOOL_OPERATOR, ';'],
            [Scanner::T_GROUP_START, '('],
            [Scanner::T_SELECTOR, 'checkFlag'],
            [Scanner::T_GROUP_END, ')'],
            [Scanner::T_GROUP_END, ')'],
        ]);
    }
}
