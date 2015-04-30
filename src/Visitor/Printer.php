<?php

namespace Ckr\Fiql\Visitor;

use Ckr\Fiql\Tree\Node;
use Ckr\Fiql\Tree\Visitor;

class Printer implements Visitor
{

    private $text;

    public function visit(Node $node)
    {
        $type = $node->getType();
        $repr = 'UNKNOWN';
        if ($node instanceof Node\Matcher) {
            $repr = 'selector=' . $node->getSelector();
        }
        $this->text .= $type . ':' . $repr;
    }

    public function getText()
    {
        return $this->text;
    }
}
