#!/bin/sh
set -eu

# Debian releases that are out of support (the PHP 7.4 and 8.0 images are based
# on bullseye) are no longer served from the main mirrors, only from the archive.
. /etc/os-release
case "${VERSION_CODENAME:-}" in
	buster|bullseye)
		printf 'deb http://archive.debian.org/debian %s main\ndeb http://archive.debian.org/debian-security %s-security main\n' \
			"$VERSION_CODENAME" "$VERSION_CODENAME" >/etc/apt/sources.list
		rm -f /etc/apt/sources.list.d/*
		printf 'Acquire::Check-Valid-Until "false";\n' >/etc/apt/apt.conf.d/99archive
		;;
esac

apt-get update
apt-get install -y --no-install-recommends git libonig-dev unzip

docker-php-ext-install mbstring

# xdebug is installed but inert unless XDEBUG_MODE is set (e.g. for coverage).
# It may not compile yet on unreleased PHP versions, so failure is non-fatal.
if pecl install xdebug >/tmp/xdebug-install.log 2>&1; then
	docker-php-ext-enable xdebug
	printf 'xdebug.mode=off\n' >"$PHP_INI_DIR/conf.d/xdebug-mode.ini"
else
	printf 'warning: could not install xdebug on this PHP version; code coverage is unavailable.\n' >&2
	tail -n 5 /tmp/xdebug-install.log >&2
fi

rm -rf /var/lib/apt/lists/*
