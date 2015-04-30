<?php

namespace Ckr\Fiql\Tree\Node;

use Ckr\Fiql\Tree\Node;

class BoolExpr extends AbstractNode
{


    const OP_AND = '&&';

    const OP_OR = '||';

    private $operator;

    private $left;

    private $right;

    public function __construct(Node $left, $operator, Node $right)
    {
        if (!in_array($operator, [self::OP_OR, self::OP_AND])) {
            throw new \InvalidArgumentException(
                'operator must be one of ' . self::OP_OR . ' and ' . self::OP_AND
            );
        }
        $this->operator = $operator;
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'bool_expr';
    }

    public function getLeftOperand()
    {
        return $this->left;
    }

    public function getRightOperand()
    {
        return $this->right;
    }

    public function getOperator()
    {
        return $this->operator;
    }
}
