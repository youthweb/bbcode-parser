# Backward compatibility promise

BBCode-Parser follows [Semantic Versioning 2](https://semver.org). This document states which parts of the library code outside this repository may rely on, and how a breaking change to them is allowed to happen. Everything that is not listed under *Covered* is not covered.

## Covered

- **Classes and interfaces** in the namespace `Youthweb\BBCodeParser`:
  - `Manager`: the constructor and the method `parse()` with their signatures.
  - `Config`.
  - `Filter\FilterInterface` and `Filter\FilterException`.
  - `Visitor\VisitorInterface`, `Visitor\VisitorCollectionInterface` and `Visitor\VisitorCollection`.

  Code outside may implement the interfaces, so adding a method to one of them is a breaking change.
- **Config keys** accepted by `Manager::parse()`, with their type and default value. Removing or renaming a key, or changing its default value, is a breaking change. Adding a key is not.
- **BBCode syntax**: the tag names and the parameters the library accepts. Removing a tag, or rejecting a notation that was accepted before, is a breaking change.
- **The Composer package name** `youthweb/bbcode-parser`.

## Not covered

- **The produced HTML.** The markup of the tags may change in any release, for example through an escaping fix. Every such change is listed in the CHANGELOG under *Changed*.
- **Internal classes**: everything in `Definition\` and `DefinitionSet\`, and the classes `Html`, `Validation` and `ValidationInterface`. They may change or disappear in any release. They are not meant to be extended, even where a class is not `final`.
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
