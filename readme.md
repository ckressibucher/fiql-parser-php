FIQL Parser for php
===================

This library provides a parser for the Feed Item Query Language.
The parser creates a syntax tree from a string expression in the Feed Item Query Language
(http://tools.ietf.org/html/draft-nottingham-atompub-fiql-00)

*Note that this library is not yet stable enough for production usage*. If you find a bug, please
help by creating an issue on github.

Basic Usage
-----------

```php
$scanner = new \Ckr\Fiql\Scanner();
$parser = new \Ckr\Fiql\Parser($scanner);
$syntaxTree = $parser->parse('field==value,second=val');
```

You can implement a custom visitor class (implementing \Ckr\Fiql\Tree\Visitor) to process
the syntax tree for your purposes, e.g. to check for matching feeds items. This library
provides only one visitor, `\Ckr\Fiql\Visitor\Printer`, which is mainly used to
visualize and compare syntax trees.

References
-----------

* FIQL definition: http://tools.ietf.org/html/draft-nottingham-atompub-fiql-00
* FIQL parser python: https://github.com/sergedomk/fiql_parser