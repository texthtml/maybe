# Type-hinting fixtures

These files exist to prove that `src/`'s type annotations carry real
information: each one deliberately triggers the static-analysis tools
(phpstan level 9, psalm, mago analyze) so the suites fail loudly if an
annotation stops being honored.

Because the issues are intentional, every one is suppressed inline and
classified with an emoji, so a reader can tell intended findings from
false positives at a glance:

- 🎯 **`@mago-expect`** — the tool genuinely reports an intended issue.
  `@mago-expect` doubles as a test: if the tool stops reporting, mago
  fails with an unfulfilled-expectation error.
- 🙈 **`@mago-ignore` / `@psalm-suppress` / `@phpstan-ignore`** — the
  tool reports something, but it is a false positive (an analyzer
  limitation). Each marker carries a reason comment. These are still
  checked: psalm's `findUnusedPsalmSuppress` and phpstan's
  `reportUnmatchedIgnoredErrors` fail the build when the issue
  disappears, so stale suppressions surface.
- 🫥 **blind spot** — a tool doesn't report an issue. Usually this is
  noticed because its siblings catch it on the same line, but that is
  not a requirement: config-level suppressions (e.g. psalm.xml mutes
  `MissingThrowsDocblock` for `tests/`) can hide what a tool would
  otherwise catch, and it can happen that no tool in the suite detects
  the issue at all. Always noted on its own comment line, naming the
  tool that is blind and why.

Comment order for multiple suppressions on one code line: the 🫥 note
lines first, then `@mago-expect`/`@mago-ignore`, then the
`@psalm-suppress` docblock, then `@phpstan-ignore` / `@phpcs:ignore`
directly above the code.

To find out what a given tool actually reports on a line, strip the
relevant pragmas in a scratch copy and run that single tool with
`--error-format=raw` (phpstan) or `--reporting-format json` (mago).
Psalm needs a full-project run to report unused suppressions.
