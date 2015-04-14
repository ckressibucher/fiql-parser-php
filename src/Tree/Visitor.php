<?php

namespace Ckr\Fiql\Tree;

/**
 * Visitor to allow multiple operations to be performed on the
 * Syntax Tree.
 */
interface Visitor {

    public function visit(Node $node);
} 