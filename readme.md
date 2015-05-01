FIQL Parser for php
===================

This library provides a parser for the Feed Item Query Language.
The parser creates a syntax tree from a string expression in the Feed Item Query Language
(http://tools.ietf.org/html/draft-nottingham-atompub-fiql-00)

*Note that this library is not yet stable enough for production usage*. If you find a bug, please
help by creating an issue on github.

Requirements
------------

This library currently does not use any third party dependencies. A PHP version >= 5.4 is required.
For testing, phpspec is used.

[![Build Status](https://travis-ci.org/ckressibucher/fiql-parser-php.svg?branch=master)](https://travis-ci.org/ckressibucher/fiql-parser-php)

Basic Usage
-----------

```php
// autoloading is psr-4 compliant, so we can use composer
require 'vendor/autoload.php';

// initialize scanner and parser
$scanner = new \Ckr\Fiql\Scanner();
$parser = new \Ckr\Fiql\Parser($scanner);

// parse expression to a syntax tree
$syntaxTree = $parser->parse('field==value,second=lt=val;requiredField');

// example visitor usage
$visitor = new \Ckr\Fiql\Visitor\Printer();
$visitor->visit($syntaxTree);

echo 'The syntax tree visualized:' . PHP_EOL;
echo $visitor->getText();
echo PHP_EOL;
```

Syntax Tree
-----------

The syntax tree is composed of nodes implementing `\Ckr\Fiql\Tree\Node`.

Visitor
--------

You can implement a custom visitor class (implementing `\Ckr\Fiql\Tree\Visitor`) to process
the syntax tree for your purposes, e.g. to check for matching feeds items. This library
provides only one visitor, `\Ckr\Fiql\Visitor\Printer`, which is mainly used to
visualize and compare syntax trees.

References
-----------

* FIQL definition: http://tools.ietf.org/html/draft-nottingham-atompub-fiql-00
* FIQL parser python: https://github.com/sergedomk/fiql_parser
