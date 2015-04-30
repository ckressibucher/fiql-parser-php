<?php

namespace Ckr\Fiql\Tree;

interface Node
{

    public function accept(Visitor $visitor);

}
