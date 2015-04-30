<?php

namespace Ckr\Fiql\Tree;

interface Node
{

    public function accept(Visitor $visitor);

    /**
     * Returns a unique string to identify the type of the node.
     *
     * @return string
     */
    public function getType();
}
