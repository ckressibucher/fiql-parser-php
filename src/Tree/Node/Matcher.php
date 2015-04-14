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

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return '';
    }
}
