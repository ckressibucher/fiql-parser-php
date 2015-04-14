<?php
/**
 * Team in medias GmbH
 */

namespace Ckr\Fiql\Tree\Node;


use Ckr\Fiql\Tree\Node;
use Ckr\Fiql\Tree\Visitor;

abstract class AbstractNode implements Node
{

    public function accept(Visitor $visitor)
    {
        $visitor->visit($this);
    }
} 