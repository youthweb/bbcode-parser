# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/).

## [Unreleased](https://github.com/youthweb/bbcode-parser/compare/1.8.0...master)

### Added

- Experimental parser engine based on `league/commonmark` that builds a node tree from BBCode (`[b]`, `[i]`, `[url]`, `[code]`), marked `@experimental`, see [#44](https://github.com/youthweb/bbcode-parser/issues/44)
- Experimental parser engine: the inline tags `[b]`, `[i]`, `[u]` and the deprecated `[F]` and `[K]`, rendered as `<strong>`, `<em>` and `<u>`, see [#48](https://github.com/youthweb/bbcode-parser/issues/48)
- Experimental parser engine: the tags `[url]`, `[YWLINK]`, `[email]`, `[img]`, `[PIC]` and the linking of bare urls with the same config keys as 1.x; an invalid or unsafe url renders as text, and the text of an `[email]` can no longer break out of the script of `protect_email`, see [#49](https://github.com/youthweb/bbcode-parser/issues/49)
- Experimental parser engine: the tags `[list]`, `[quote]`, `[q]`, the deprecated `[Z]` and `[noparse]`, rendered as valid HTML (a quote is no longer inside a `<p>`, lists and quotes nest up to five levels, the option of a quote is read up to the bracket and escaped), see [#50](https://github.com/youthweb/bbcode-parser/issues/50)
- Experimental parser engine: the tags `[color]`, `[size]`, `[video]`, `[v]` and `[youtube]` with the validation and config keys of 1.x; a tag without content renders nothing, and the url of a video is linked with its text escaped, see [#51](https://github.com/youthweb/bbcode-parser/issues/51)
- Experimental parser engine: Markdown input and the entry point `Engine::toHtml()` with `InputMode` to choose BBCode or Markdown per call; both render through the same renderers, raw HTML is escaped, and headings and thematic breaks can be switched off, see [#52](https://github.com/youthweb/bbcode-parser/issues/52)

### Changed

- Require `league/commonmark` ^2.0
- Require `youthweb/urllinker` ^2.3 (was ^1.4); its newer top-level-domain list changes which bare addresses such as `example.web` or `foo.helsinki` are turned into links
- Require `cache/void-adapter` ^3.0 (was ^1.1), so the library now requires `psr/cache` ^3: a cache pool given as the config option `cacheitempool` has to implement the `psr/cache` 3 interface (with return types), and a project that still has `psr/cache` 1 or 2 has to update it

### Fixed

- Stop raising PHP deprecations for `[color]`, `[email]`, `[list]`, `[q]`, `[size]` and `[url]` tags without a parameter (passing `null` to `trim()` and `preg_match()`); the produced HTML is unchanged

### Removed

- Support for PHP 7.4, 8.0 and 8.1; PHP 8.2 or newer is required
- Dependency on the abandoned `jakeasmith/http_build_url`

## [1.8.0](https://github.com/youthweb/bbcode-parser/compare/1.7.1...1.8.0) - 2026-11-21

### Added

- Support for PHP 8.1, 8.2, 8.3, 8.4 and 8.5, the test suite runs on all of them
- A [backward compatibility promise](docs/bc-promise.md) that states which parts of the library are covered

### Changed

- Mark `Definition`, `DefinitionSet`, `Html`, `Validation` and `ValidationInterface` as `@internal`, they are not covered by the backward compatibility promise

### Fixed

- Declare the parameter of `Manager::__construct()` explicitly nullable, the implicit nullable type is deprecated in PHP 8.4

## [1.7.1](https://github.com/youthweb/bbcode-parser/compare/1.7.0...1.7.1) - 2021-11-24

### Fixed

- Don't escape HTML that is setted through a Visitor

## [1.7.0](https://github.com/youthweb/bbcode-parser/compare/1.6.0...1.7.0) - 2021-11-23

### Added

- New code definitions for bold, italic and underline

### Changed

- Replace Travic-CI with Github Actions

## [1.6.0](https://github.com/youthweb/bbcode-parser/compare/1.5.0...1.6.0) - 2021-03-05

### Added

- Add support for PHP 7.4 and 8.0

### Changed

- Drop support for PHP 7.2 and 7.3

## [1.5.0](https://github.com/youthweb/bbcode-parser/compare/1.4.1...1.5.0) - 2019-10-01

### Added

- Add support for PHP 7.3

### Changed

- Drop support for PHP 5.6, 7.0 and 7.1

### Fixed

- Fixed bug in multidimensional lists

## [1.4.1](https://github.com/youthweb/bbcode-parser/compare/1.4.0...1.4.1) - 2018-03-15

### Fixed

- Call visitors in Manager after parsing the text

## [1.4.0](https://github.com/youthweb/bbcode-parser/compare/1.3.0...1.4.0) - 2018-03-14

### Added

- The `Youthweb\BBCodeParser\Manager` constructor expects an optional `Youthweb\BBCodeParser\Visitor\VisitorCollectionInterface` object to add custom Vistors

## [1.3.0](https://github.com/youthweb/bbcode-parser/compare/1.2.1...1.3.0) - 2018-01-26

### Changed

- Update cache/void-adapter to 1.0
- Update youthweb/urllinker to 1.2
- Switch code style to PSR-2

## [1.2.1](https://github.com/youthweb/bbcode-parser/compare/1.2.0...1.2.1) - 2017-10-28

### Fixed

- Fixed case missmatches in `QuoteOption` and `YwlinkOption`

## [1.2.0](https://github.com/youthweb/bbcode-parser/compare/1.1.0...1.2.0) - 2017-06-28

### Changed

- Allow dependencies to cache/void-adapter 0.3 and 0.4

## [1.1.0](https://github.com/youthweb/bbcode-parser/compare/1.0.1...1.1.0) - 2016-09-06

### Added

- Autolink Urls without the need of [url]
- Escape all html characters
- VisitorInterface for injecting your custom Visitor
- Simple VisitorSmiley created as example
- PHP 7.1 support

### Changed

- Drop PHP 5.5 support

## [1.0.1](https://github.com/youthweb/bbcode-parser/compare/1.0.0...1.0.1) - 2016-05-05

### Added

- A PSR6 CacheItemPool can be injected via config

## [1.0.0](https://github.com/youthweb/bbcode-parser/compare/c4163941a543d79e2179fa54559ba06bc9e1f4a4...1.0.0) - 2016-05-04

### Added

- Initial version, separated from youthweb.net
