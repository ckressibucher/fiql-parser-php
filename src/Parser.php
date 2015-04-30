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
        // TODO
//        $selector = $this->tokens[$this->pointer++];
//        list($type, $value) = $selector;
//        $comp = $nextToken[1];
//        $arg =
    }

    protected function parseMatcher()
    {
        $token = array_pop($this->stack);
        if (null === $token) {
            throw new UnexpectedTokenException('No token was found on stack. Selector token was expected.');
        }
        list($type, $value) = $token;
        if ($type !== Scanner::T_SELECTOR) {
            throw new UnexpectedTokenException('Selector token was expected');
        }
        $matcher = new Matcher($value);
        $this->stack[] = $matcher;
    }

    protected function getLookAhead($offset = 1)
    {
        if (isset($this->tokens[$this->pointer + $offset])) {
            return $this->tokens[$this->pointer + $offset];
        }
        return false;
    }
}
