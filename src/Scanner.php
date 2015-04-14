<?php

namespace Ckr\Fiql;

/**
 * TODO decode plus and percentage encoding
 * TODO perform scanning iteratively instead of recursively (using a stack to keep track of the current state)
 * TODO allow setting additional compare operators
 */
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

    /**
     * Character class of special characters defined by FIQL
     *
     * @var string
     */
    protected static $regexFiqlDelim = '[\!\$\'\*\+]';

    /**
     * Definition of comparision operator. This is initialized in
     * the static `initialize` method
     *
     * @var string
     */
    protected static $regexCompareOp;

    /**
     * Definition of an argument character.
     * Initialized in the static `initialize` method.
     *
     * @var string
     */
    protected static $regexArgChar;

    /**
     * Definition of an argument.
     * Initialized in the static `initialize` method.
     *
     * @var string
     */
    protected static $regexArgument;

    /**
     * Definition of a selector.
     * Initialized in the static `initialize` method.
     *
     * @var string
     */
    protected static $regexSelector;


    /**
     * key: FIQL comparison operator
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

    /**
     * key: FIQL logical operators
     * val: Arbitrary identifier for that operator.
     *
     * @var array
     */
    protected $logicalOperators = [
        ';' => self::OP_AND,
        ',' => self::OP_OR
    ];

    /**
     * Stores the next token.
     *
     * @var string
     */
    protected $nextToken;

    /**
     * List of already parsed tokens.
     *
     * @var string[]
     */
    protected $tokens;

    /**
     * @var bool
     */
    protected static $isInitialized = false;

    /**
     * Main method to tokenize a given FIQL Expression.
     *
     * @param   string $queryString
     * @return  string[]
     * @throws  SyntaxException
     */
    public function scan($queryString)
    {
        static::initialize();
        $this->remaining = $queryString;
        $this->tokens = [];
        $this->scanExpression();
        if ($this->remaining !== '') {
            throw new SyntaxException(
                'Remaining string is expected to be empty after parsing the expression'
            );
        }
        return $this->tokens;
    }

    /**
     * Initializes combined regex patterns.
     */
    protected static function initialize()
    {
        if (!static::$isInitialized) {
            static::$regexSelector = '(?<selector>'
                . '(' . static::$regexUnreserved . '|' . static::$regexPctEncoding . ')+)';
            static::$regexCompareOp = '(?<comp_op>(=[A-Za-z]*|' . static::$regexFiqlDelim . ')=)';

            static::$regexArgChar = '(' . static::$regexUnreserved . '|'
                . static::$regexPctEncoding . '|' . static::$regexFiqlDelim . '|'
                . '=)';
            static::$regexArgument = '(?<arg>' . static::$regexArgChar . '+)';

            static::$isInitialized = true;
        }
    }

    /**
     * Scans an expression from the beginning of the remaining string.
     *
     * @throws SyntaxException
     */
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
            $regex = '\)';
            $this->expect($regex); // close group
            $this->shiftToken();
        }
    }

    /**
     * Scans a constraint from the beginning of the remaining string.
     *
     * @throws SyntaxException
     */
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

    /**
     * Checks if the given regex matches the beginning of the remaining string.
     * If so, it is set to nextToken. Otherwise, an exception is thrown.
     *
     * @param   string  $regexExpected
     * @throws  SyntaxException
     */
    protected function expect($regexExpected)
    {
        $pattern = '/^(?<token>' . $regexExpected . ')/';
        if (preg_match($pattern, $this->remaining, $matches)) {
            $this->nextToken = $matches['token'];
            return;
        }
        throw new SyntaxException('Expected pattern ' . $regexExpected . ' did not match');
    }

    /**
     * Checks if the remaining string starts with a group opening parenthesis,
     * and, if so, sets this character to `nextToken` and returns true.
     * Otherwise, false is returned.
     *
     * @return bool
     */
    protected function isGroupStart()
    {
        if (strlen($this->remaining) === 0) {
            return false;
        }
        if ($this->remaining[0] === '(') {
            $this->nextToken = '(';
            return true;
        }
        return false;
    }

    /**
     * Tries to parse a selector from the beginning of the remaining string.
     * Sets nextToken to the selector value and returns true, it it succeeds,
     * else false.
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

    /**
     * Checks if the next token is a comparison operator. Sets `nextToken`
     * and returns true, if it is. Else, false is returned.
     *
     * @return bool
     */
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

    /**
     * Checks if the next token is a logical operator. If so,
     * `nextToken` is set to the operator string, and true is
     * returned. Otherwise, false is returned.
     *
     * @return bool
     */
    protected function isLogicalOperator()
    {
        if (strlen($this->remaining) === 0) {
            return false;
        }
        $operators = array_flip($this->logicalOperators);
        if (in_array($this->remaining[0], $operators)) {
            $this->nextToken = $this->remaining[0];
            return true;
        }
    }

    /**
     * Checks if the next token is an argument. If so, `nextToken`
     * is set to the argument value, and true is returned. Otherwise,
     * false is returned.
     *
     * @return bool
     */
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
        if ($len === strlen($this->remaining)) {
            $this->remaining = '';
        } else {
            $this->remaining = substr($this->remaining, $len);
        }
    }
}
