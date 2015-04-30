<?php

namespace Ckr\Fiql\Tree\Node;

class Constraint extends DyadicExpr
{

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $argument;

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'constraint';
    }

    /**
     * @param string $field
     * @param string $operator
     * @param string $argument
     */
    public function __construct($field, $operator, $argument)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->argument = $argument;
    }

    /**
     * @return string
     */
    public function getArgument()
    {
        return $this->argument;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

}
