<?php

namespace Ckr\Fiql\Visitor;

use Ckr\Fiql\Tree\Node;
use Ckr\Fiql\Tree\Visitor;

class Printer implements Visitor
{

    private $text;

    private $level = 0;

    public function visit(Node $node)
    {
        if (!isset($this->text)) {
            $this->text = '';
        }
        for ($i = 0; $i < $this->level; $i++) {
            $this->text .= "\t";
        }
        $this->text .= $node->getType() . ':';
        if ($node instanceof Node\Matcher) {
            $this->text .= $node->getSelector();
        } elseif ($node instanceof Node\Constraint) {
            $this->text .= $node->getField() . $node->getOperator() . $node->getArgument();
        } elseif ($node instanceof Node\BoolExpr) {
            $this->text .= $node->getOperator() . "\n";
            $this->level++;
            $this->visit($node->getLeftOperand());
            $this->text .= "\n";
            $this->visit($node->getRightOperand());
            $this->level--;
        }
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
