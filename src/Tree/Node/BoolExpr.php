<?php

namespace Ckr\Fiql\Tree\Node;

use Ckr\Fiql\Tree\Node;

class BoolExpr extends AbstractNode
{


    const OP_AND = '&&';

    const OP_OR = '||';

    /**
     * @var string
     */
    private $operator;

    /**
     * @var Node
     */
    private $left;

    /**
     * @var Node
     */
    private $right;

    /**
     * @param Node   $left
     * @param string $operator
     * @param Node   $right
     */
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

    /**
     * @return Node
     */
    public function getLeftOperand()
    {
        return $this->left;
    }

    /**
     * @return Node
     */
    public function getRightOperand()
    {
        return $this->right;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }
}
