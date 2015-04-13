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
}
