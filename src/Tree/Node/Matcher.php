<?php

namespace Ckr\Fiql\Tree\Node;

use Ckr\Fiql\Tree\Node;

/**
 * This node represents a selector only matcher.
 * It yields true, if the selector matches a node in
 * the entry to which it is applied
 */
class Matcher extends AbstractNode
{

    /**
     * @var string
     */
    private $selector;

    /**
     * @param string $selector
     */
    public function __construct($selector)
    {
        $this->selector = $selector;
    }

    /**
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
    }
}
