# Releasing

How a new version of the library gets released. A release is **developer-initiated and never agent-launched**: the developer picks the moment. An agent may prepare the release up to the gate (the release pull request) and stops there.

Steps 6 and 7 are developer-only: tagging and the GitHub release cannot be undone, because Packagist picks up a tag at once. Don't hand them to an agent.

PHP, Composer and the PHP tools run only inside the Docker dev container (`make` targets), never on the host.

## Entry

The developer decides a release is warranted, and `master` is in the state they intend to release. The section `[Unreleased]` of `CHANGELOG.md` lists every change since the last release: if it is empty or incomplete, fill it in a pull request first, in English, under the categories *Added*, *Changed*, *Deprecated*, *Removed*, *Fixed* and *Security*. The CHANGELOG names what concerns users of the library. Changes to the development setup (Docker, CI, tooling) do not belong in it.

## Steps

**1. Propose the version** [agent proposes, developer confirms]
Read `[Unreleased]` and classify it: any breaking change under `docs/bc-promise.md` means a major version, new behaviour or a new supported PHP version a minor version, fixes only a patch version. Present the version and the reason.
_Done_: the developer confirms the version or corrects it.

**2. Supported-range check** [blocking]
If `[Unreleased]` claims a change of the supported PHP versions, `require.php` in `composer.json` and `php-versions` in `.github/workflows/php.yml` must match that claim. A mismatch is a hold: fix it in a pull request before the release.
_Done_: no claim has a mismatch.

**3. Prepare the release branch**
From up-to-date `master`, create `release-<X.Y.Z>` and change `CHANGELOG.md` only:

- Move the content of `[Unreleased]` unchanged into a new section `## [X.Y.Z](https://github.com/youthweb/bbcode-parser/compare/<previous>...X.Y.Z) - <YYYY-MM-DD>`. `<previous>` is the last released tag.
- Keep an empty `## [Unreleased](https://github.com/youthweb/bbcode-parser/compare/X.Y.Z...master)` above it.

```bash
git fetch origin
git checkout -b release-<X.Y.Z> origin/master
```

Commit it as `Release <X.Y.Z>`, always with the full number, for example `Release 1.8.0`.
_Done_: the diff shows only the moved section, the new heading and the changed `[Unreleased]` link. No entry was dropped or reworded.

**4. Validate**
Run `make test` inside the Docker dev container (PHP 7.4).
_Done_: the tests pass.

**5. Open the pull request** [agent proposes a draft, developer approves and merges]
Open the pull request against `master`, titled `Release <X.Y.Z>`. Its description is the new CHANGELOG section. The checks must be green: all jobs of the PHP matrix and the Roave backward compatibility check. Merge it with a merge commit.
_Done_: the pull request is merged and its checks were green.

**6. Tag** [developer only]
Tag the merge commit on `master`, not the commit on the release branch. The tag is annotated, named `X.Y.Z` without a `v`, and pushed on its own:

```bash
git fetch origin
git checkout master && git pull
git tag -a <X.Y.Z> -m "Release <X.Y.Z>"
git push origin <X.Y.Z>
```

_Done_: `git ls-remote --tags origin <X.Y.Z>` shows the tag.

**7. GitHub release** [developer only]
Create the release from the tag. Its title is `<X.Y.Z>` and its description is the CHANGELOG section of that version. Create it right after the tag, not later. It is neither a draft nor a prerelease.
_Done_: the release page shows the version with its description.

**8. Aftermath**
- Packagist picks the tag up through its webhook; no action is needed. Check that `https://packagist.org/packages/youthweb/bbcode-parser` shows the new version.
- Work off what the release unlocks. Right now: ticket #40 "Compare the BC check against the newest tag again after the next release" (remove `--from=d5057e5` from `.github/workflows/bc-check.yml`), and #39 "Update the known issues in AGENTS.md".

_Done_: the new version is on Packagist and the follow-up tickets are worked or planned.
