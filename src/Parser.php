<?php

namespace Ckr\Fiql;

use Ckr\Fiql\Parser\ParseException;
use Ckr\Fiql\Parser\UnexpectedTokenException;
use Ckr\Fiql\Tree\Node;
use Ckr\Fiql\Tree\Node\Matcher;
use Ckr\Fiql\Tree\Operator;

class Parser
{

    /**
     * @var array
     */
    protected $tokens;

    protected $pointer;

    protected $stack;

    protected $currentToken;

    public function parse(Scanner $scanner, $queryString)
    {
        $this->stack = [];
        $this->pointer = 0;
        $this->tokens = $scanner->scan($queryString);

        $this->stack[] = $this->tokens[$this->pointer++];
        $this->parseExpr();

        if (count($this->stack) !== 1) {
            throw new ParseException('Unexpected number of elements on stack after parsing');
        }
        $node = current($this->stack);
        if (!$node instanceof Node) {
            throw new ParseException('Unexpected value on stack after parsing: expected a node');
        }
        return $node;
    }

    protected function parseExpr()
    {
        $token = end($this->stack);
        switch ($token[0]) {
            case Scanner::T_GROUP_START:
                // TODO
                break;
            case Scanner::T_GROUP_END:
                // TODO
                break;
            case Scanner::T_SELECTOR:
                $nextToken = $this->getLookAhead();
                if ($nextToken && $nextToken[0] === Scanner::T_COMP_OPERATOR) {
                    $this->parseConstraint();
                } else {
                    $this->parseMatcher();
                }
                break;
            default:
                throw new UnexpectedTokenException();
        }
    }

    protected function parseConstraint()
    {
        $selector = $this->popSelectorTokenFromStack();

        $operator = $this->tokens[$this->pointer++];
        if ($operator[0] !== Scanner::T_COMP_OPERATOR) {
            throw new UnexpectedTokenException('Expected compare operator');
        }
        $argument = $this->tokens[$this->pointer++];
        if ($argument[0] !== Scanner::T_ARGUMENT) {
            throw new UnexpectedTokenException('Expected argument');
        }
        $constraint = new Node\Constraint($selector[1], $operator[1], $argument[1]);
        $this->stack[] = $constraint;
    }

    protected function parseMatcher()
    {
        $token = $this->popSelectorTokenFromStack();
        $matcher = new Matcher($token[1]);
        $this->stack[] = $matcher;
    }

    /**
     * @return array
     * @throws UnexpectedTokenException
     */
    protected function popSelectorTokenFromStack()
    {
        $token = $this->popTokenFromStack();
        if ($token[0] !== Scanner::T_SELECTOR) {
            throw new UnexpectedTokenException('Selector token was expected');
        }
        return $token;
    }

    /**
     * @return array
     * @throws UnexpectedTokenException
     */
    protected function popTokenFromStack()
    {
        $token = array_pop($this->stack);
        if (null === $token || !is_array($token)) {
            throw new UnexpectedTokenException('No token was found on stack. Selector token was expected.');
        }
        return $token;
    }

    protected function getLookAhead($offset = 0)
    {
        if (isset($this->tokens[$this->pointer + $offset])) {
            return $this->tokens[$this->pointer + $offset];
        }
        return false;
    }
}
