# Markdown input

The engine built on `league/commonmark` renders BBCode and Markdown. The syntax is chosen per call, a text is either BBCode or Markdown, they are not mixed. The API is covered by the [backward compatibility promise](bc-promise.md): the config keys below and the Markdown syntax of this page.

```php
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Engine;
use Youthweb\BBCodeParser\InputMode;

$engine = new Engine(new Config());

$engine->toHtml('[b]text[/b]');                        // BBCode is the default
$engine->toHtml('**text**', InputMode::Markdown);
```

In Markdown mode `[b]` is text, in BBCode mode `**` is text.

## What Markdown can do

| Markdown | HTML |
| --- | --- |
| `**a**`, `__a__` | `<strong>` |
| `*a*`, `_a_` | `<em>` |
| `~~a~~`, `~a~` | `<del>` |
| `` `a` `` | `<code>` |
| `[a](url)`, `[a][ref]`, `<url>`, a bare url | link, rendered like `[url]` |
| `<a@b.org>`, `[a](mailto:a@b.org)` | email link, rendered like `[email]` |
| `![a](url)` | image, rendered like `[img]` |
| `> a` | quote, rendered like `[quote]` |
| `- a`, `1. a` | list, rendered like `[list]` and `[list=1]` |
| fenced and indented code | code block, rendered like `[code]` |
| `# a`, `a` with `===` below | heading |
| `---` | thematic break |

Links, images, quotes, lists and code run through the same renderers as the BBCode tags. The rules of the config (valid and safe urls, `short_url`, `target`, the image check) and the escaping apply in both modes, and the same construct gives the same HTML.

Not part of it: tables, task lists, footnotes and attributes. The alt text and the title of an image and the title of a link are dropped, and the info string of a fenced code block as well.

## Raw HTML

There is no raw HTML. `<b>` is text and is escaped, in every place.

## Differences to BBCode

- Line breaks: a soft break (a line break in a paragraph) renders the config `soft_break`, default `<br />`. In BBCode mode it stays `<br />` followed by a line break, like in the parser. A hard break (two spaces or a backslash before the line break) renders `<br />` in both modes.
- Headings are moved down by `heading_offset` (default 2): `#` renders `<h3>`, and the level stops at `<h6>`. With `0` the levels are as written.
- Quotes and lists are nested up to five levels, a quote and a list count one level each. A deeper marker stays text.
- A list item with a single paragraph is rendered without `<p>`, loose lists as well.

The config is read when the `Engine` is built for the Markdown mode, so build a new `Engine` after changing it.

## Config keys

| Key | Default | |
| --- | --- | --- |
| `markdown.headings` | `true` | `false` turns headings off: `# a` stays the text `# a`, and so does the setext form |
| `markdown.thematic_breaks` | `true` | `false` turns thematic breaks off: `---` stays text |
| `heading_offset` | `2` | levels to move headings down |
| `soft_break` | `<br />` | HTML of a soft break, Markdown mode only |

Their defaults are applied where the keys are read, they are not part of the default config of `Config`. The other keys (`parse_urls`, `callbacks.url_content.*`, `callbacks.image.*`) work as in the BBCode mode.

A project adds its own extensions (smileys, mentions of users, filters for images and videos) to the `Engine`, see [extension-points.md](extension-points.md).
