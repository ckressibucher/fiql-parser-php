<?php

namespace spec\Ckr\Fiql\Tree\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatcherSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $selector = 'my_field';
        $this->beConstructedWith($selector);
        $this->shouldHaveType('Ckr\Fiql\Tree\Node\Matcher');
    }

    function its_getSelector_returns_the_selector()
    {
        $selector = 'my_fieldname';
        $this->beConstructedWith($selector);
        $this->getSelector()->shouldReturn($selector);
    }
}
