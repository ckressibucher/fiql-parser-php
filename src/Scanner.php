<?php

namespace Ckr\Fiql;

/**
 * TODO allow setting additional compare operators (e.g. regex)
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

    // token types
    const T_GROUP_START = 1;    // '('
    const T_GROUP_END = 2;      // ')'
    const T_COMP_OPERATOR = 10;
    const T_BOOL_OPERATOR = 15;
    const T_SELECTOR = 20;
    const T_ARGUMENT = 25;

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

    protected static $regexConstraint;


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
    protected $boolOperators = [
        ';' => self::OP_AND,
        ',' => self::OP_OR
    ];

    /**
     * List of already parsed tokens.
     *
     * @var string[]
     */
    protected $tokens;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var bool
     */
    protected static $isInitialized = false;

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

            static::$regexConstraint = '/' . static::$regexSelector
                . '(' . static::$regexCompareOp . static::$regexArgument . ')?/';

            static::$isInitialized = true;
        }
    }

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
        $this->tokens = [];
        $this->level = 0;

        do {
            list($pre, $selector, $compOp, $arg, $post) = $this->matchConstraint($queryString);
            $this->processPreamble($pre);
            if ($selector) {
                $selector = $this->decodeHex($selector);
                $this->tokens[] = [self::T_SELECTOR, $selector];
            }
            if ($compOp) {
                $this->tokens[] = [self::T_COMP_OPERATOR, $compOp];
                $arg = $this->decodeHex($arg);
                $this->tokens[] = [self::T_ARGUMENT, $arg];
            }
            $queryString = $post;
        } while ($queryString !== '');

        if ($this->level !== 0) {
            throw new SyntaxException('Parenthesis were not opened/closed correctly');
        }

        return $this->tokens;
    }

    /**
     * @param $source
     * @return array
     * @throws SyntaxException
     */
    protected function matchConstraint($source)
    {
        if (preg_match(static::$regexConstraint, $source, $matches)) {
            $constraint = $matches[0];
            $pos = strpos($source, $constraint);
            $pre = substr($source, 0, $pos);
            $postLen = strlen($source) - ($pos + strlen($constraint));
            if ($postLen > 0) {
                $post = substr($source, $pos + strlen($constraint));
            } else {
                $post = '';
            }
            $selector = $matches['selector'];
            if (isset($matches['comp_op'])) {
                $compOp = $matches['comp_op'];
                $arg = $matches['arg'];
            } else {
                $compOp = $arg = null;
            }
            return [
                $pre,
                $selector,
                $compOp,
                $arg,
                $post
            ];
        } elseif (preg_match('/^\)*$/', $source)) {
            // end of expression: closing parenthesis
            return [$source, null, null, null, ''];
        } else {
            throw new SyntaxException('source did not match a valid constraint');
        }
    }

    /**
     * @param string $preamble
     */
    protected function processPreamble($preamble)
    {
        for ($i = 0; $i < strlen($preamble); $i++) {
            $char = $preamble[$i];
            switch ($char) {
                case '(':
                    $this->tokens[] = [self::T_GROUP_START, $char];
                    $this->level++;
                    break;
                case ')';
                    $this->tokens[] = [self::T_GROUP_END, $char];
                    $this->level--;
                    break;
                case ';':
                case ',':
                $this->tokens[] = [self::T_BOOL_OPERATOR, $char];
                    break;
                default:
                    new SyntaxException();
            }
        }
    }

    protected function decodeHex($input)
    {
        $decoded = preg_replace_callback('|%([0-9A-Fa-f]{2})|', function($m) {
            return chr(hexdec($m[1]));
        }, $input);
        return $decoded;
    }

}
