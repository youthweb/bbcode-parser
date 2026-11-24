# HTML differences between 1.x and the league engine

The league engine (`Engine`, BBCode mode) writes valid HTML, and compatibility with the 1.x output comes second. Where the two differ, it is on purpose: either 1.x wrote invalid markup, or the engine follows a simpler rule.

`tests/Integration/Engine/EngineCorpusTest.php` runs 1.x (`Manager`) and the engine over every file in `tests/Fixtures/Corpus` and compares the normalised HTML. A case is either equal, or listed in `tests/Fixtures/Corpus/differences.php` with the IDs of the sections below. A new difference, or a listed case that became equal, turns the test red. The test also checks that this document has a section for exactly the IDs that are listed.

This document lists the differences the corpus shows. The tag tests in `tests/Integration/Parser` pin more edge cases of single tags (options that are not valid, urls, colors, sizes, videos) to both outputs; where they differ, the name of the case says so. The examples below show the normalised HTML, so 1.x appears with `<strong>` where it writes `<b>`.

## What the comparison evens out

The test normalises only what is decided and changes no text:

- `<b>` and `<i>` of 1.x are `<strong>` and `<em>`.
- Whitespace next to block tags (`p`, `blockquote`, `cite`, `ul`, `ol`, `li`, `pre`, `div`, headings) is layout only. This includes a space before a quote that 1.x leaves inside the paragraph.

## Differences

### D01 Quotes and lists are blocks

1.x puts a quote or a list inside a `<p>`, and a `<br />` inside a list. Both are invalid. In the engine they are siblings of paragraphs, and the text of a quote is a paragraph.

```text
[quote]Zitat[/quote]
1.x:    <p><blockquote title="Zitat"><cite></cite>Zitat</blockquote></p>
engine: <blockquote title="Zitat"><p>Zitat</p></blockquote>

[list][*]a[list][*]b[/list][*]c[/list]
1.x:    <ul type="disc"><li>a<p><ul type="disc"><br /><li>b</li><br /></ul></p></li><li>c</li></ul>
engine: <ul><li>a<ul><li>b</li></ul></li><li>c</li></ul>
```

### D02 No empty `<cite>`

1.x writes `<cite></cite>` for a quote without a name. The engine writes the element only when the quote has a name.

```text
[quote]Zitat[/quote]
1.x:    <blockquote title="Zitat"><cite></cite>Zitat</blockquote>
engine: <blockquote title="Zitat"><p>Zitat</p></blockquote>
```

### D03 A bullet list has no `type` attribute

`<ul type="disc">` is obsolete HTML and `disc` is the default. Ordered lists keep their `type` and `start`.

```text
[list][*]eins[*]zwei[/list]
1.x:    <ul type="disc"><li>eins</li><li>zwei</li></ul>
engine: <ul><li>eins</li><li>zwei</li></ul>
```

### D04 Blank lines in a list item make paragraphs

1.x keeps the blank line as text in the item. The engine makes a paragraph for each part. An item with a single paragraph stays tight, without `<p>`.

```text
[list][*]A

B[*]C[/list]
1.x:    <ul type="disc"><li>A

B</li><li>C</li></ul>
engine: <ul><li><p>A</p><p>B</p></li><li>C</li></ul>
```

### D05 Blank lines separate paragraphs

A line that is empty or holds only spaces or tabs ends a paragraph, and any number of them is one break. 1.x turns a line with whitespace into a line break and leaves a stray `<br />` at the start of the paragraph after several blank lines.

```text
A\n\n\n\n\nB
1.x:    <p>A</p><p><br />\nB</p>
engine: <p>A</p><p>B</p>
```

### D06 Inline tags end with their paragraph or block

An inline tag does not reach over a paragraph break or into a quote or list. It is closed there, and its closing tag later stays text. 1.x produced markup that is not nested.

```text
[b]a\n\nb[/b]
1.x:    <p><strong>a</p><p>b</strong></p>
engine: <p><strong>a</strong></p><p>b[/b]</p>
```

### D07 A bare url inside another tag is a link

1.x shows the link markup as text when the url stands inside a tag like `[b]`.

```text
[b]http://example.org[/b]
1.x:    <p><strong>&lt;a target=&quot;_blank&quot; href=&quot;http://example.org&quot;&gt;http://example.org&lt;/a&gt;</strong></p>
engine: <p><strong><a target="_blank" href="http://example.org">http://example.org</a></strong></p>
```

### D08 Text is shown as typed

The engine treats the input as text: an `&` is always written as `&amp;`, also where it starts something that looks like an entity. What someone types is what the reader sees. 1.x left entities such as `&amp;`, `&copy;` or `&#60;` alone, so the browser showed `&`, © and `<`, and it wrote a raw `&` in the text of a link. Code is shown as typed too.

```text
a & b &amp; c
1.x:    <p>a &amp; b &amp; c</p>
engine: <p>a &amp; b &amp;amp; c</p>

http://example.org/a?b=1&c=2
1.x:    <a target="_blank" href="http://example.org/a?b=1&amp;c=2">http://example.org/a?b=1&c=2</a>
engine: <a target="_blank" href="http://example.org/a?b=1&amp;c=2">http://example.org/a?b=1&amp;c=2</a>
```

### D09 Quotes and lists nest at most five levels

A quote or list tag below the fifth level stays text, with its closing tag. 1.x nested without a limit.

```text
[quote]1[quote]2[quote]3[quote]4[quote]5[quote]6[/quote]…
engine: …<blockquote title="Zitat"><p>5[quote]6[/quote]</p></blockquote>…
```
