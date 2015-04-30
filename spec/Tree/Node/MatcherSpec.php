<?php

namespace spec\Ckr\Fiql\Tree\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatcherSpec extends ObjectBehavior
{

    function let() {
        $this->beConstructedWith('dont_care');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Ckr\Fiql\Tree\Node\Matcher');
    }

    function it_should_implement_the_node_interface()
    {
        $this->shouldHaveType('Ckr\Fiql\Tree\Node');
    }

    function its_getSelector_returns_the_selector()
    {
        $selector = 'my_fieldname';
        $this->beConstructedWith($selector);
        $this->getSelector()->shouldReturn($selector);
    }

    function its_getType_should_return_the_string_matcher()
    {
        $this->beConstructedWith('dont_care');
        $this->getType()->shouldReturn('matcher');
    }
}
