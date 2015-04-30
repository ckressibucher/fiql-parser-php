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

    protected $scanner;

    /**
     * @param Scanner $scanner
     */
    public function __construct(Scanner $scanner)
    {
        $this->scanner = $scanner;
    }

    /**
     * @param Scanner $scanner
     * @param $queryString
     *
     * @return Tree\Node;
     *
     * @throws ParseException
     * @throws UnexpectedTokenException
     */
    public function parse($queryString)
    {
        $this->stack = [];
        $this->pointer = 0;
        try {
            $this->tokens = $this->scanner->scan($queryString);
        } catch (SyntaxException $e) {
            throw new ParseException('Syntax exception was detected during parsing', 0, $e);
        }

        $this->next();
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
                $this->parseGroupedExpression();
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

    protected function parseGroupedExpression()
    {
        $this->popGroupStartTokenFromStack();
        $this->next();
        $this->parseExpr();
        $this->expectType(Scanner::T_GROUP_END);
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
    protected function popGroupStartTokenFromStack()
    {
        $token = $this->popTokenFromStack();
        if ($token[0] !== Scanner::T_GROUP_START) {
            throw new UnexpectedTokenException('Group start token was expected');
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

    /**
     * Get next token and push it to the stack. Advance the pointer.
     *
     * @throws UnexpectedTokenException
     */
    protected function next()
    {
        if (!isset($this->tokens[$this->pointer])) {
            throw new UnexpectedTokenException('Expected additional tokens.');
        }
        $this->stack[] = $this->tokens[$this->pointer++];
    }

    /**
     * Advances the pointer, and checks if the retrieved token is of the expected type.
     *
     * @param int $type
     * @throws UnexpectedTokenException
     */
    protected function expectType($type)
    {
        if (!isset($this->tokens[$this->pointer])) {
            throw new UnexpectedTokenException('Expected additional tokens.');
        }
        $token = $this->tokens[$this->pointer++];
        if ($token[0] !== $type) {
            throw new UnexpectedTokenException('Token of type ' . $type . ' was expected.');
        }
    }

    /**
     * Return a lookahead token without advancing the pointer
     *
     * @param int $offset
     * @return bool
     */
    protected function getLookAhead($offset = 0)
    {
        if (isset($this->tokens[$this->pointer + $offset])) {
            return $this->tokens[$this->pointer + $offset];
        }
        return false;
    }
}
