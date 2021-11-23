# BBCode-Parser

[![Latest Version](https://img.shields.io/github/release/youthweb/bbcode-parser.svg)](https://github.com/youthweb/bbcode-parser/releases)
[![Software License](https://img.shields.io/badge/license-GPL3-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/youthweb/bbcode-parser.svg?branch=master)](https://travis-ci.org/youthweb/bbcode-parser)
[![Coverage Status](https://coveralls.io/repos/github/youthweb/bbcode-parser/badge.svg?branch=master)](https://coveralls.io/github/youthweb/bbcode-parser?branch=master)[![Build Status](https://github.com/youthweb/bbcode-parser/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/youthweb/bbcode-parser/actions)

A BBCode-to-HTML parser for youthweb.net

## Install

Via Composer

``` bash
$ composer require youthweb/bbcode-parser
```

## Usage

```php
use Youthweb\BBCodeParser\Manager;

$text = '[h1]Hello World![/h1]

This is a [i]simple[/i] test to demonstrate the [b]BBCodeParser[/b].';

$parser = new Manager();
$config = ['parse_headlines' => true];

echo $parser->parse($text, $config);

// "<h1>Hello World!</h1>
//  <p>This is a <i>simple</i> test to demonstrate the <b>BBCodeParser</b>.</p>"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ phpunit
```

## Contributing

Please feel free to submit bugs or to fork and sending Pull Requests.

## License

GPL3. Please see [License File](LICENSE.md) for more information.
