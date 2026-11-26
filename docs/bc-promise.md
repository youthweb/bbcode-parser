# Backward compatibility promise

BBCode-Parser follows [Semantic Versioning 2](https://semver.org). This document states which parts of the library code outside this repository may rely on, and how a breaking change to them is allowed to happen. Everything that is not listed under *Covered* is not covered.

## Covered

- **Classes and interfaces** in the namespace `Youthweb\BBCodeParser`:
  - `Manager`: the constructor and the method `parse()` with their signatures.
  - `Config`.
  - `Engine`, `InputMode`, `TextReplacementListener` and the public nodes of the engine: `Extension\Node\Image`, `Video`, `Email`, `Underline`, `Color`, `Size`, `BBList`, `BBListItem`, `BBHeading`, `Smiley`, `Username` and `TrustedHtml` (see [extension-points.md](extension-points.md)).
  - `Filter\FilterInterface` and `Filter\FilterException`.
  - `Visitor\VisitorInterface`, `Visitor\VisitorCollectionInterface` and `Visitor\VisitorCollection`.

  Code outside may implement the interfaces, so adding a method to one of them is a breaking change.
- **Config keys** accepted by `Manager::parse()` and by the `Engine`, including the keys of [markdown-input.md](markdown-input.md), with their type and default value. Removing or renaming a key, or changing its default value, is a breaking change. Adding a key is not.
- **BBCode syntax**: the tag names and the parameters the library accepts. Removing a tag, or rejecting a notation that was accepted before, is a breaking change. The same holds for the Markdown syntax described in [markdown-input.md](markdown-input.md).
- **The use of `league/commonmark` by the engine**: the nodes extend its classes and a project passes its `ExtensionInterface` to the `Engine`. A new major version of `league/commonmark` that can break such an extension is a breaking change and needs a major release of this library.
- **The Composer package name** `youthweb/bbcode-parser`.

## Not covered

- **The produced HTML.** The markup of the tags may change in any release, for example through an escaping fix. Every such change is listed in the CHANGELOG under *Changed*.
- **Internal classes**: everything in `Definition\` and `DefinitionSet\`, the classes `Html`, `Validation` and `ValidationInterface`, and the engine classes marked `@internal`: everything in `Parser\`, the renderers, extensions and listeners in `Extension\`. They may change or disappear in any release. They are not meant to be extended, even where a class is not `final`.
- **Experimental API**: classes, interfaces and methods marked `@experimental` in their docblock. They may change or disappear in any release until the mark is removed. Removing the mark is announced in the CHANGELOG under *Changed*. At present nothing is marked.
- **Private and protected members** of any class.
- **Exception messages**: their wording may change.
- **The development setup**: tests, CI, tooling configuration, `docs/` and the Docker dev container.

## How a breaking change happens

1. A minor release deprecates the part: the CHANGELOG lists it under *Deprecated*, the code carries `@deprecated` with the replacement, and the replacement is available in the same release.
2. The next major release may remove it, at the earliest one minor release after the deprecation. There is no fixed time span. The CHANGELOG lists the removal as **BREAKING**.

A deprecation does not trigger a runtime notice. A later minor release may add an `E_USER_DEPRECATED` notice to a deprecated part; that is not a breaking change.

The tags `[F]`, `[K]` and `[Z]` are deprecated, with removal at the earliest in the next major release.

## Supported PHP versions

The supported range is `require.php` in `composer.json`. A minor release may raise the minimum PHP version, or drop a PHP version. This is listed in the CHANGELOG and is not a breaking change: Composer checks `require.php` on installation, so a project running an older PHP version keeps resolving to the earlier release.

## Supported releases

Only the latest minor release of the current major version receives fixes.
