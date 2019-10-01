# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/).

## [Unreleased]

### Added

- Add support for PHP 7.3

### Changed

- Drop support for PHP 5.6, 7.0 and 7.1

### Fixed

- Fixed bug in multidimensional lists

## [1.4.1] - 2018-03-15

### Fixed

- Call visitors in Manager after parsing the text

## [1.4.0] - 2018-03-14

### Added

- The `Youthweb\BBCodeParser\Manager` constructor expects an optional `Youthweb\BBCodeParser\Visitor\VisitorCollectionInterface` object to add custom Vistors

## [1.3.0] - 2018-01-26

### Changed

- Update cache/void-adapter to 1.0
- Update youthweb/urllinker to 1.2
- Switch code style to PSR-2

## [1.2.1] - 2017-10-28

### Fixed

- Fixed case missmatches in `QuoteOption` and `YwlinkOption`

## [1.2.0] - 2017-06-28

### Changed

- Allow dependencies to cache/void-adapter 0.3 and 0.4

## [1.1.0] - 2016-09-06

### Added

- Autolink Urls without the need of [url]
- Escape all html characters
- VisitorInterface for injecting your custom Visitor
- Simple VisitorSmiley created as example
- PHP 7.1 support

### Changed

- Drop PHP 5.5 support

## [1.0.1] - 2016-05-05

### Added

- A PSR6 CacheItemPool can be injected via config

## [1.0.0] - 2016-05-04

### Added

- Initial version, separated from youthweb.net

[Unreleased]: https://github.com/youthweb/bbcode-parser/compare/1.4.1...HEAD
[1.4.1]: https://github.com/youthweb/bbcode-parser/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/youthweb/bbcode-parser/compare/1.3.0...1.4.0
[1.3.0]: https://github.com/youthweb/bbcode-parser/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/youthweb/bbcode-parser/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/youthweb/bbcode-parser/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/youthweb/bbcode-parser/compare/1.0.1...1.1.0
[1.0.1]: https://github.com/youthweb/bbcode-parser/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/youthweb/bbcode-parser/compare/c4163941a543d79e2179fa54559ba06bc9e1f4a4...1.0.0
