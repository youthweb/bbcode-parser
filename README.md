# BBCode-Parser

[![Latest Version](https://img.shields.io/github/release/youthweb/bbcode-parser.svg)](https://github.com/youthweb/bbcode-parser/releases)
[![Software License](https://img.shields.io/badge/license-GPL3-brightgreen.svg)](LICENSE.md)
[![Build Status](https://github.com/youthweb/bbcode-parser/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/youthweb/bbcode-parser/actions)

A BBCode-to-HTML parser for youthweb.net

## Install

Via Composer

``` bash
$ composer require youthweb/bbcode-parser
```

## Usage

The library has two parts that run side by side:

- **The engine** (`Engine`) is the default for new code. It is built on [league/commonmark](https://commonmark.thephpleague.com), renders BBCode and Markdown and writes valid HTML.
- **The parser** (`Manager`) is the original implementation. It stays for compatibility with existing code.

The API of the engine and of the parser is covered by the [backward compatibility promise](docs/bc-promise.md).

### Engine (default)

The input mode is chosen per call: a text is either BBCode or Markdown.

```php
use Youthweb\BBCodeParser\Engine;
use Youthweb\BBCodeParser\InputMode;

$engine = new Engine();

echo $engine->toHtml('This is a [i]simple[/i] test to demonstrate the [b]BBCodeParser[/b].');

// "<p>This is a <em>simple</em> test to demonstrate the <strong>BBCodeParser</strong>.</p>"

echo $engine->toHtml('This is a *simple* test to demonstrate the **BBCodeParser**.', InputMode::Markdown);

// "<p>This is a <em>simple</em> test to demonstrate the <strong>BBCodeParser</strong>.</p>"
```

- [HTML differences between the parser and the engine](docs/engine-html-differences.md)
- [Extension points of the engine](docs/extension-points.md)
- [Markdown input](docs/markdown-input.md)

### Parser (compatibility)

`Manager` renders BBCode only and takes the config keys, filters and visitors of the original implementation.

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
$ vendor/bin/phpunit
```

## Contributing

Please feel free to submit bugs or to fork and sending Pull Requests.

## License

GPL3. Please see [License File](LICENSE.md) for more information.
