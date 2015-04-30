<?php

namespace Ckr\Fiql\Tree\Node;

class Constraint extends DyadicExpr
{
    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'constraint';
    }
}
