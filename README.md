# BBCode-Parser

A BBCode-to-HTML parser for youthweb.net

## Usage

```php
use Youthweb\BBCodeParser\Manager;

$text = '[b]Hello World![/b]';

$parser = new Manager();

echo $parser->parse($text);

// "<b>Hello World!</b>"
```
