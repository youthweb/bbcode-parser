# bbcode-parser – Notes for agents

PHP library `youthweb/bbcode-parser`: converts BBCode to HTML. See `README.md` for purpose and usage, `composer.json` for dependencies, namespace, PHP versions and license.

## Structure

The entry point is `Manager::parse()`. Learn the rest from `src/` and `tests/`; do not rely on a description here.

## PHP only in Docker

PHP, Composer and every PHP tool (`vendor/bin/*`, PHPUnit, PHPStan, PHP CS Fixer, Rector) run **only inside the Docker dev container, never on the host system**. Do not invoke `php`, `composer` or `vendor/bin/*` on the host, not even for a quick check. Use the `make` targets from `docs/dev-environment.md`. If a command needs PHP, run it through `make shell` or `make composer ARGS="..."`.

## Development

- The default branch is `master` (not `main`).
- Use the Makefile for every task, see `docs/dev-environment.md` for targets and PHP versions. `make codestyle` **rewrites files**.
- `composer.lock` is gitignored.

## Known issues

- Little typing. Docblocks partly wrong (namespaces without leading backslash), comments mix German and English, `DefaultSet` has an orphaned docblock.
- Security-sensitive (not reviewed in detail): HTML escaping and placeholders run as text replacement after rendering. `Image`, `Url` and `V` output external URLs.
- README badges and CI point at `master`.

## Git

No Claude attribution in commits (global rule).

Commits, PRs, issues and docs are written in English.

Never commit on `master`: every change goes on a branch and reaches `master` through a pull request.

## Pull requests

- Open a pull request against `master`. It closes its ticket with `Closes #<n>` in the description; a pull request that delivers only a part of a ticket says "Part of #<n>" and what stays open.
- Merge only with green checks: every job of the PHP matrix and the Roave backward compatibility check.
- The description has three sections, short, without a preamble:
  - **Summary**: the smallest view that shows the change, for example a diff sketch, a call tree, a file tree or a few lines of pseudocode, with the ticket and, where the code changes, the one fact that makes it safe and how far it was proved.
  - **Evidence**: before and after, as test output or the failing and passing test. Name the checks that ran. Numbers, rule names and levels are copied from the tool output being quoted, not written from memory.
  - **Merge Danger**: whether the change is a one-way or a two-way door (can it be walked back?) and its blast radius in a word or two (for example consumers of the library, produced HTML, CI only, none), with the ramifications where there are some.
- **CHANGELOG**: a pull request adds its entry to `[Unreleased]` in `CHANGELOG.md` itself when the change concerns users of the library: public API (including the engine), behaviour, produced HTML (under *Changed*), dependencies, supported PHP versions, deprecations, fixes. Use one line in English under *Added*, *Changed*, *Deprecated*, *Removed*, *Fixed* or *Security*. Changes to the development setup (Docker, CI, tooling), docs and refactorings without a visible effect get no entry. The description states it: "CHANGELOG: entry added" or "CHANGELOG: none needed, because ...". A review requests changes where the statement is missing or wrong.

## Agent skills

### Issue tracker

Issues and specs live in the GitHub Issues of `youthweb/bbcode-parser`, operated via the `gh` CLI. Refactoring tickets carry the label `refactoring`. See `docs/agents/issue-tracker.md`.

### Triage labels

Default vocabulary (`needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`). See `docs/agents/triage-labels.md`.

### Domain docs

Single-context: one `GLOSSARY.md` and `docs/adr/` at the repo root. See `docs/agents/domain.md`.

### Refactoring ideas

When you notice a possible refactoring while doing other work, post it straight away as a comment on the open Housekeeping ticket (`docs/agents/issue-tracker.md`, **Housekeeping**): where it is, what would change, and why. It needs no question to the human first. When two Housekeeping tickets are open, use the younger.

### Releasing

Releases are developer-initiated, never agent-launched. Prepare up to the release pull request and stop there; tagging and the GitHub release are the developer's. See `docs/agents/releasing.md`.
