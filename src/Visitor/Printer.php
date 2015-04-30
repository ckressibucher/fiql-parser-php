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
            $repr = $node->getSelector();
        } elseif ($node instanceof Node\Constraint) {
            $repr = $node->getField() . $node->getOperator() . $node->getArgument();
        }
        $this->text = (isset($this->text) ? $this->text : '') . $type . ':' . $repr;
    }

    public function getText()
    {
        return isset($this->text) ? $this->text : '';
    }

    public function reset()
    {
        unset($this->text);
    }
}
