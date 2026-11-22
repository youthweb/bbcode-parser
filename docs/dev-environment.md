# Development Environment

All PHP tooling runs inside Docker. PHP itself is never installed on the host and must not be invoked there.

## Requirements

- Docker
- `make`

## First run

```bash
make install
```

builds the dev image and installs the Composer dependencies into `vendor/`. `make test` also runs `composer install` if `vendor/` is missing, so the first invocation can simply be:

```bash
make test
```

## Make targets

| Target | Description |
| --- | --- |
| `make build` | Build the dev container image |
| `make install` | Install Composer dependencies |
| `make test` | Run the PHPUnit test suite (default target) |
| `make phpunit` | Run PHPUnit |
| `make phpstan` | Run PHPStan (`composer phpstan`) |
| `make codestyle` | **Fix** the code style with PHP CS Fixer (`composer php-cs-fixer`); it rewrites files, check `git diff` afterwards |
| `make rector` | Run Rector (`composer rector`) with the PHP 7.4, dead code and code quality rule sets and the backward-compatible type declaration rules (`art4/rector-bc-library`); it **rewrites files**, so check `git diff` afterwards. `make rector ARGS="--dry-run"` only shows the changes |
| `make coverage` | Run PHPUnit with an HTML coverage report in `build/code-coverage/` |
| `make coverage-floor` | Run PHPUnit with coverage and fail when the line coverage is below the value in `.coverage-floor`. Needs Xdebug, which does not exist for PHP 7.4: `make coverage-floor PHP_VERSION=8.3` |
| `make shell` | Open an interactive shell inside the container |
| `make composer ARGS="..."` | Run any Composer command, e.g. `make composer ARGS="update"` |
| `make clean` | Remove `build/`, `vendor/`, `composer.lock` and the tool caches |

## Coverage floor

`.coverage-floor` holds the lowest line coverage (in percent) a run may reach. CI fails when a fresh run is below it. The value only climbs: when coverage has grown, raise it in a reviewed commit to the figure PHPUnit reports; never lower it to make a run pass. Tests state what they cover (`forceCoversAnnotation`), so the figure counts a class only where a test of it covers it; the rules are in `tests/README.md`.

## PHP version

The container runs PHP `7.4`, the lowest supported version. To run another version, pass `PHP_VERSION`, for example:

```bash
make clean install phpunit PHP_VERSION=8.5
```

Each version gets its own image (`bbcode-parser-php:<version>`). `vendor/` is shared, so run `make clean install` when you switch versions. The cross-version matrix runs in GitHub Actions.

## How it works

- The image is built from the official `php:<version>-cli` image and installs `mbstring`, `git`, `unzip` and Composer. Xdebug is installed but disabled; `make coverage` enables it.
- The repository is bind-mounted at `/app`; no project files are baked into the image (see `.dockerignore`).
- The container runs as your host user, so `vendor/`, `build/` and the caches stay owned by you.
- The PHP 7.4 and 8.0 images are based on Debian bullseye, which is out of support. `scripts/docker/php/install-tools.sh` switches apt to `archive.debian.org` for those images.

## Troubleshooting

- **Permission problems on `vendor/` or caches** — something ran as root. Remove them with `docker run --rm -v "$PWD:/app" busybox rm -rf /app/vendor /app/composer.lock` and reinstall with `make install`.
