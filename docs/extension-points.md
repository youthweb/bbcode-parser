# Extension points of the engine

The engine built on `league/commonmark` has no visitor and filter interfaces like `Manager`. A project adds league extensions to the `Engine`. Their renderers and their listeners on the `DocumentParsedEvent` work in both modes, BBCode and Markdown. Inline and block parsers work in the Markdown mode only, the BBCode mode has its own parser. The API is covered by the [backward compatibility promise](bc-promise.md).

```php
use Youthweb\BBCodeParser\Config;
use Youthweb\BBCodeParser\Engine;

$engine = new Engine(new Config(), [
    new ImageFilterExtension(),
    new UsernameExtension($users, $router),
]);
```

An extension implements `League\CommonMark\Extension\ExtensionInterface`. Inside of `register()` it adds renderers and listeners to the environment. The extensions are added after the ones of the engine.

## The nodes

These nodes are public and covered by the [backward compatibility promise](bc-promise.md). The renderers and listeners of the engine are internal.

| Node | Created by | Content |
| --- | --- | --- |
| `Image` | `[img]`, `[PIC]`, `![](…)` | `getLiteral()`: the content of the tag or the url |
| `Video` | `[video]`, `[v]`, `[youtube]` | `getLiteral()`: the content of the tag |
| `Email`, `Underline`, `Color`, `Size`, `BBList`, `BBListItem` | the tags of the same name | |
| `BBHeading` | `[h1]` to `[h6]` | `getLevel()`: the level as written, `heading_offset` does not apply |
| `Smiley` | an extension | `getCode()`, `getUrl()`: rendered as `<img src="url" alt="code" title="code" />` |
| `Username` | an extension | `getName()` without the `@`, `getUrl()`: rendered as `<a href="url">@name</a>` |
| `TrustedHtml` | an extension | `getLiteral()`: HTML that is rendered as it is |

The parser never creates `Smiley`, `Username` and `TrustedHtml`. The values are escaped, and an unsafe url (`javascript:`) leaves the text instead of the link or the image. `TrustedHtml` is not escaped: use it only for HTML that the extension has made itself or trusts, never for text from a user.

## Porting the four cases

| parser | engine |
| --- | --- |
| `filter.image`, `filter.video` (`FilterInterface`) | a renderer for `Image` or `Video` with a priority above 0 |
| `visitor.smiley`, a `VisitorInterface` for usernames | a listener on `DocumentParsedEvent` that extends `TextReplacementListener` |

### A filter

The renderer returns the HTML, or `null` if the node is not for it. Then the default renderer takes over, like a `FilterException` in the parser.

```php
final class ImageFilterExtension implements ExtensionInterface, NodeRendererInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addRenderer(Image::class, $this, 10);
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): ?string
    {
        if (! $node instanceof Image) {
            return null;
        }

        $id = trim($node->getLiteral());

        if (! ctype_digit($id)) {
            return null; // an url: the default renderer
        }

        // …look up the file and return the HTML
    }
}
```

`closestParentOfType('url')` of the parser becomes a walk up the parents: `for ($p = $node->parent(); $p !== null; $p = $p->parent())` and a check for `League\CommonMark\Extension\CommonMark\Node\Inline\Link`. A Markdown image (`![](42)`) is the same `Image` node, so the filter applies to it too.

### Smileys and usernames

`TextReplacementListener` hands the text of every text node to `replace()`. The method returns the parts that take its place: a string stays text, a node is put in as it is. Return `[$text]` to change nothing, and `[]` to remove the text.

```php
final class UsernameExtension extends TextReplacementListener implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, $this);
    }

    protected function replace(string $text): array
    {
        // split at the mentions, return strings and `new Username($name, $url)`
    }
}
```

Text in `[noparse]` and inside of a link or an `[email]` is left alone, so a replacement is never inside of an anchor (the parser did put a link into the text of a link). `replace()` sees one text node at a time: a code that the parser has split into two text nodes, for example at a `*` or `_`, is not found. Code blocks and inline code have no text nodes. Use `TrustedHtml` for what no node fits, for example the HTML of an emoji library.

### Order

The engine runs its own listeners first: the Markdown tree (priority 110) and the autolinker (100). A listener with the default priority 0 runs after them, so a bare url is a link already and a smiley in its text stays text. A higher priority runs earlier.

## Config

- `filter.image`, `filter.video`, `visitor.smiley`, `visitor.url` and `parse_smilies` have no effect on the engine. A project gives the extensions it wants.
- `parse_urls` still switches the autolinker.
- `embed_videos`, `embed_maxwidth` and `embed_maxheight` stay in `Config`. The engine does not read them, like the parser, but an extension such as a video filter may read them from the `Config` it was given.
- The library has no list of smileys. The project brings its own.
- `parse_default` has no effect on the engine, it always renders the tags.

## What the engine does not do

The parser turns ` yw ` into `<acronym title="Youthweb">YW</acronym>`. The engine does not, and `<acronym>` is obsolete HTML. A project that wants it replaces the text with a listener that extends `TextReplacementListener`, see [Smileys and usernames](#smileys-and-usernames), and returns a `TrustedHtml` node with `<abbr title="Youthweb">YW</abbr>`.
