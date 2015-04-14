<?php

namespace Ckr\Fiql\Tree;

class Operator
{

    // compare operators
    const COMPARE_EQUAL = 1;
    const COMPARE_NOT_EQUAL = 2;
    const COMPARE_GREATER_THAN = 3;
    const COMPARE_GREATER_EQUAL = 4;
    const COMPARE_LESS_THAN = 5;
    const COMPARE_LESS_EQUAL = 6;

    // logical operators
    const OP_AND = 20;
    const OP_OR = 21;

    /**
     * The type of the operator
     *
     * @var int
     */
    protected $type;

    /**
     * @param int $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function __toString()
    {
        switch ($this->type) {
            case self::COMPARE_EQUAL:
                return '=';
            case self::COMPARE_NOT_EQUAL:
                return '!=';
            case self::COMPARE_GREATER_THAN:
                return '>';
            case self::COMPARE_GREATER_EQUAL:
                return '>=';
            case self::COMPARE_LESS_THAN:
                return '<';
            case self::COMPARE_LESS_EQUAL:
                return '<=';
            case self::OP_OR:
                return '||';
            case self::OP_AND:
                return '&&';
            default:
                throw new \RuntimeException('Unknown type, cannot render');
        }
        return ''; // to make the ide happy
    }
}
