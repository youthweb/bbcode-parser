# bbcode-parser – Notes for agents

PHP library `youthweb/bbcode-parser`: converts BBCode to HTML. Built on `jbbcode/jbbcode`, uses `youthweb/urllinker`. Consumed by `youthweb`. Namespace `Youthweb\BBCodeParser`, PHP `^7.4 || ^8.0`, license GPL-3.0-or-later. State at analysis (2026-11-21): release 1.7.1 (2021-11-24); the project has been dormant since.

## Structure

- `Manager::parse($text, array $config = [])` is the entry point. Flow: merge config → build definition sets (`DefaultSet`, optionally `HeadlineSet` for `[h1]`–`[h6]`) → visitors (URL autolinking, custom visitors, smileys) → `getAsHtml()` → `<p>` paragraphs (marker `<!-- no_p -->`) → replace placeholders (`{{{lt}}}`, `{{{gt}}}`, `{{{quot}}}`, ` yw ` → `<acronym>`).
- `src/Definition/`: one class per tag, with an `…Option` variant for `[tag=value]` (Bold, Italic, Underline, Code, Noparse, List, Q/Quote/Z, Email, Url, Ywlink, Image/Pic, V/Video/Youtube, Color, Size). Deprecated: `[F]`, `[K]`, `[Z]`.
- `src/Visitor/`: `VisitorUrl` (urllinker), `VisitorSmiley`, `VisitorCollection`.
- `src/Config.php` (nested config), `src/Validation.php` and `src/Filter/` (input validation). A PSR-6 cache can be injected via config; the default is `cache/void-adapter`.
- Tests: `tests/Unit/` (per definition/visitor), `tests/Integration/` (`ParsingTest`, `SmileyTest`, `QuoteTest`), mocks in `tests/Fixtures/`.

## PHP only in Docker

PHP, Composer and every PHP tool (`vendor/bin/*`, PHPUnit, PHPStan, PHP CS Fixer, Rector) run **only inside the Docker dev container, never on the host system**. Do not invoke `php`, `composer` or `vendor/bin/*` on the host, not even for a quick check. Use the `make` targets listed below. If a command needs PHP, run it through `make shell` or `make composer ARGS="..."`.

## Development

- The default branch is `master` (not `main`). Other remote branches: `v2.x`, `init-commonmark-parser`, `custom_visitors`. A v2 based on CommonMark was started; its state is unclear.
- The dev container runs PHP 7.4, the lowest supported version. Use the Makefile: `make build`, `make install`, `make test` (default; PHPUnit), `make phpunit`, `make phpstan`, `make codestyle` (this **rewrites files**), `make coverage`, `make shell`, `make composer ARGS="..."`, `make clean`. Other PHP versions: `make test PHP_VERSION=8.5` (after `make clean install`). See `docs/dev-environment.md`.
- Under the hood: `vendor/bin/phpunit` (suites `all`, `unit`, `integration`), `composer php-cs-fixer` (PSR-12), `composer phpstan` (level 1). `composer.lock` is gitignored.
- Test status: all 182 tests pass on PHP 7.4 and 8.5 (8.5 with `trim(): Passing null` deprecations in `Definition/Color.php` and `Definition/Email.php`). The code does not match the current PHP CS Fixer ruleset (77 files would change), so never run `make codestyle` on the whole tree without intending that diff.

## Known issues (as of 2026-11-21)

- Outdated toolchain: PHPUnit ^9, PHPStan ^1.2 (level 1 only), CI on `actions/checkout@v2` and `actions/cache@v2`. The CI matrix ends at PHP 8.0; PHP 8.1+ is neither tested nor declared.
- `Manager::__construct(VisitorCollectionInterface $visitorCollection = null)` uses an implicit nullable type (deprecated as of PHP 8.4).
- Little typing. Docblocks partly wrong (namespaces without leading backslash), comments mix German and English, `DefaultSet` has an orphaned docblock.
- `jakeasmith/http_build_url` is a polyfill for `pecl_http`. Whether and where it is needed is unchecked.
- Security-sensitive (not reviewed in detail): HTML escaping and placeholders run as text replacement after rendering. `Image`, `Url` and `V` output external URLs.
- README badges and CI point at `master`.

## Git

No Claude attribution in commits (global rule).

Commits, PRs, issues and docs are written in English.

## Agent skills

### Issue tracker

Issues and specs live in the GitHub Issues of `youthweb/bbcode-parser`, operated via the `gh` CLI. See `docs/agents/issue-tracker.md`.

### Triage labels

Default vocabulary (`needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`). See `docs/agents/triage-labels.md`.

### Domain docs

Single-context: one `GLOSSARY.md` and `docs/adr/` at the repo root. See `docs/agents/domain.md`.
