<?php

namespace Ckr\Fiql\Tree\Node;

use Ckr\Fiql\Tree\Node;
use Ckr\Fiql\Tree\Visitor;

class TrueExpr extends AbstractNode
{

    public function getType()
    {
        return 'true';
    }

}
