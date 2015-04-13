<?php

namespace Ckr\Fiql;

class Scanner
{

    // compare operators
    const COMPARE_EQUAL = '=';
    const COMPARE_NOT_EQUAL = '!=';
    const COMPARE_GREATER_THAN = '>';
    const COMPARE_GREATER_EQUAL = '>=';
    const COMPARE_LESS_THAN = '<';
    const COMPARE_LESS_EQUAL = '<=';

    // logical operators
    const OP_AND = '&&';
    const OP_OR = '||';

    /**
     * @var string
     */
    protected $remaining;

    /**
     * Regex for unreserved characters as described in
     * @see https://tools.ietf.org/html/rfc3986#section-2.3
     *
     * @var string
     */
    protected static $regexUnreserved = '[a-zA-Z0-9~\._-]';

    /**
     * Percentage encoding
     *
     * @var string
     */
    protected static $regexPctEncoding = '%[A-Fa-f0-9]{2}';

    protected static $regexFiqlDelim = '[\!\$\'\*\+]';

    protected static $regexCompareOp;

    protected static $regexArgChar;

    protected static $regexArgument;

    /**
     * key: fiql comparison operator
     * val: arbitrary (but unique) identifier
     *
     * (this may be extended to allow additional comparison operators in theory)
     *
     * @var array
     */
    protected $compareOperators = [
        '==' => self::COMPARE_EQUAL,
        '!=' => self::COMPARE_NOT_EQUAL,
        '=gt=' => self::COMPARE_GREATER_THAN,
        '=ge=' => self::COMPARE_GREATER_EQUAL,
        '=lt=' => self::COMPARE_LESS_THAN,
        '=le=' => self::COMPARE_LESS_EQUAL,
    ];

    protected $logicalOperators = [
        ';' => self::OP_AND,
        ',' => self::OP_OR
    ];

    protected static $regexSelector;

    /**
     * Stores the last parsed token
     *
     * @var string
     */
    protected $nextToken;

    protected $tokens;

    protected static $isInitialized = false;

    public function scan($queryString)
    {
        self::initialize();
        $this->remaining = $queryString;
        $this->tokens = [];
        $this->scanExpression();
        return $this->tokens;
    }

    protected static function initialize()
    {
        if (!static::$isInitialized) {
            self::$regexSelector = '(?<selector>'
                . '(' . self::$regexUnreserved . '|' . self::$regexPctEncoding . ')+)';
            self::$regexCompareOp = '(?<comp_op>(=[A-Za-z]*|' . self::$regexFiqlDelim . ')=)';

            self::$regexArgChar = '(' . self::$regexUnreserved . '|'
                . self::$regexPctEncoding . '|' . self::$regexFiqlDelim . '|'
                . '=)';
            self::$regexArgument = '(?<arg>' . self::$regexArgChar . '+)';

            static::$isInitialized = true;
        }
    }

    protected function scanExpression()
    {
        if ($isOpenGroup = $this->isGroupStart()) {
            $this->shiftToken();
        }
        if ($this->isSelector()) {
            $this->scanConstraint();
        } else {
            // must be a sub expression
            if (!$isOpenGroup) {
                // if no group was started, then the first token must be a
                // selector, starting a constraint
                throw new SyntaxException('Selector was expected');
            }
            $this->scanExpression();
        }
        while ($this->isLogicalOperator()) {
            $this->shiftToken();
            $this->scanExpression();
        }
        if ($isOpenGroup) {
            $this->expect(')'); // close group
            $this->shiftToken();
        }
    }

    protected function scanConstraint()
    {
        if (!$this->isSelector()) {
            throw new SyntaxException('Expected a selector');
        }
        $this->shiftToken();

        if ($this->isComparisonOperator()) {
            $this->shiftToken();
            if (!$this->isArgument()) {
                throw new SyntaxException('An argument token was expected');
            }
            $this->shiftToken();
        }
    }

    protected function expect($regexExpected)
    {
        $pattern = '/^(?<token>' . $regexExpected . ')';
        if (preg_match($pattern, $this->remaining, $matches)) {
            $this->nextToken = $matches['token'];
            return;
        }
        throw new SyntaxException('Expected pattern ' . $regexExpected . ' did not match');
    }

    protected function isGroupStart()
    {
        if ($this->remaining[0] === '(') {
            $this->nextToken = '(';
            return true;
        }
        return false;
    }

    protected function getNext()
    {
        if ($this->remaining === '') {
            return false;
        }
        if ($this->parseSelector()) {
            return $this->nextToken;
        } elseif ($this->parseComparisonOperator()) {
            return $this->nextToken;
        } elseif ($this->parseArgument()) {
            return $this->nextToken;
        }
        return false;
    }

    /**
     * Ties to parse a selector from the beginning of the remaining string.
     * Returns true, it it succeeds, else false
     *
     * @return bool
     */
    protected function isSelector()
    {
        $pattern = '/^' . self::$regexSelector . '/';
        if (preg_match($pattern, $this->remaining, $matches)) {
            $this->nextToken = $matches['selector'];
            return true;
        }
        return false;
    }

    protected function isComparisonOperator()
    {
        $pattern = '/^' . self::$regexCompareOp . '/';
        if (preg_match($pattern, $this->remaining, $matches)) {
            $comp = $matches['comp_op'];
            if (isset($this->compareOperators[$comp])) {
                $this->nextToken = $comp;
                return true;
            }
        }
        return false;
    }

    protected function isLogicalOperator()
    {
        $operators = array_flip($this->logicalOperators);
        if (in_array($this->remaining[0], $operators)) {
            $this->nextToken = $this->remaining[0];
            return true;
        }
    }

    protected function isArgument()
    {
        $pattern = '/^' . self::$regexArgument . '/';
        if (preg_match($pattern, $this->remaining, $matches)) {
            $this->nextToken = $matches['arg'];
            return true;
        }
        return false;
    }

    /**
     * appends nextToken to tokens array and strips this token
     * from the remaining string
     */
    protected function shiftToken()
    {
        $this->tokens[] = $this->nextToken;
        $this->reduceRemaining();
    }

    /**
     * strips the last token from beginning of remaining string
     */
    protected function reduceRemaining()
    {
        $idxLast = count($this->tokens) - 1;
        $len = strlen($this->tokens[$idxLast]);
        $this->remaining = substr($this->remaining, $len);
    }
}
