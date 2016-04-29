# BBCode-Parser

[![Latest Version](https://img.shields.io/github/release/youthweb/bbcode-parser.svg)](https://github.com/youthweb/bbcode-parser/releases)
[![Software License](https://img.shields.io/badge/license-GPL3-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/youthweb/bbcode-parser.svg?branch=master)](https://travis-ci.org/youthweb/bbcode-parser)
[![Coverage Status](https://coveralls.io/repos/github/youthweb/bbcode-parser/badge.svg?branch=master)](https://coveralls.io/github/youthweb/bbcode-parser?branch=master)

A BBCode-to-HTML parser for youthweb.net

## Usage

```php
use Youthweb\BBCodeParser\Manager;

$text = '[b]Hello World![/b]';

$parser = new Manager();

echo $parser->parse($text);

// "<b>Hello World!</b>"
```
