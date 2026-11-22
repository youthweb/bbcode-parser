# Tests

Run them with `make test`; the coverage floor with `make coverage-floor PHP_VERSION=8.3` (see `docs/dev-environment.md`).

## Layout

- `tests/Unit/` mirrors `src/`: one test class per class.
- `tests/Integration/` runs real collaborators together, entering through `Manager` or a parser.
- `tests/Fixtures/` holds helpers shared by the tests.

## Coverage metadata

`phpunit.xml` sets `forceCoversAnnotation="true"` and `failOnRisky="true"`: a test without `@covers` is risky and fails the run, and code counts as covered only where a test covers it.

A unit test covers exactly one class, the one it tests; everything else it runs it uses. An integration test covers the class it enters through. Put the `@covers` annotation in the docblock of the test class.
