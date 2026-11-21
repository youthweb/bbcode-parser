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
| `make coverage` | Run PHPUnit with an HTML coverage report in `build/code-coverage/` |
| `make shell` | Open an interactive shell inside the container |
| `make composer ARGS="..."` | Run any Composer command, e.g. `make composer ARGS="update"` |
| `make clean` | Remove `build/`, `vendor/`, `composer.lock` and the tool caches |

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
