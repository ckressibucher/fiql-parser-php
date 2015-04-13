<?php

namespace spec\Ckr\Fiql;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ScannerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Scanner');
    }

    function it_returns_an_array_of_tokens()
    {
        $this->scan('selector')->shouldBeEqualTo(['selector']);
    }

    function it_recognizes_a_selector()
    {
        /* @see https://tools.ietf.org/html/rfc3986#section-2.3 */
        $exampleSelector = 'lettersALPHA456-._~';
        $this->scan($exampleSelector)->shouldBeEqualTo([$exampleSelector]);
    }

    function it_recognizes_a_constraint()
    {
        $exampleConstraint = 'a==b';
        $this->scan($exampleConstraint)->shouldBeEqualTo(['a', '==', 'b']);
    }

    function it_recognizes_a_group()
    {
        $exampleGroup = '(selector)';
        $this->scan($exampleGroup)->shouldBeEqualTo(['(', 'selector', ')']);
    }

    function it_recognizes_an_and_operator()
    {
        $example = 'a;b';
        $this->scan($example)->shouldBeEqualTo(['a', ';', 'b']);
    }

    function it_recognizes_an_or_operator()
    {
        $example = 'a,b';
        $this->scan($example)->shouldBeEqualTo(['a', ',', 'b']);
    }

    function it_tokenizes_a_complex_example()
    {
        $example = 'abc==5;(xyz=gt=3,field!=value;(checkFlag))';
        $this->scan($example)->shouldBeEqualTo([
            'abc',
            '==',
            '5',
            ';',
            '(',
            'xyz',
            '=gt=',
            '3',
            ',',
            'field',
            '!=',
            'value',
            ';',
            '(',
            'checkFlag',
            ')',
            ')'
        ]);
    }
}
