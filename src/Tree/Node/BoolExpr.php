<?php

namespace Ckr\Fiql\Tree\Node;

class BoolExpr extends AbstractNode
{
    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'bool_expr';
    }
}
