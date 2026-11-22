# Housekeeping

Rhythm: weekly

Recurring maintenance for this project. One Housekeeping ticket is always open, made from the tasks
below. Ideas for a refactoring are left on it as comments. A new task goes above the last one.

## Tasks

- Scan the Git history for committed secrets: the whole history the first time, afterwards the commits
  added since the previous Housekeeping ticket's scan. Note the commit scanned up to.
- Re-check the project's quality tooling: is every tool set up so far still in place and running, and is
  there a further one to set up? File a ticket for each gap.
- Audit the third-party dependencies for known vulnerabilities with `make composer ARGS="audit"` and
  update each one with an advisory to a patched version.
- Update the dependencies with `make composer ARGS="update"` and record the output.
- After the update, run PHPStan (`make phpstan`) and fix newly surfaced deprecation warnings in scope.
- Look for a newer PER-CS version than the one `.php-cs-fixer.dist.php` pins (`vendor/bin/php-cs-fixer
  list-sets` inside `make shell`); where there is one, switch to it, measure, and set each rule that
  now reports findings to `false` with `Not yet:` and its count. Only the config changes, never the code.
- Create the next Housekeeping ticket from this file, due one week from today.
