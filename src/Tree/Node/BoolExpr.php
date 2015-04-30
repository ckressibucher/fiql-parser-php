<?php

namespace Ckr\Fiql\Tree\Node;

class BoolExpr extends DyadicExpr
{
    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'bool_expr';
    }
}
