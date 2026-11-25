# AGENTS.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Module Does

Magento 2 module (`LiveChat_LiveChat`) that integrates LiveChat into a Magento store:
- Connect an existing LiveChat account or create a new one (admin config page)
- Inject the LiveChat JS snippet into the storefront
- Expose visitor and cart data to LiveChat via frontend controllers

## Layout

- repo root — the Magento module (what ships; Packagist reads `composer.json` here). PSR-4 root `LiveChat\LiveChat\` is the repo root. `README.md` is user-facing and ships; `build/README.md` is for contributors. Paths marked `export-ignore` in `.gitattributes` (`build/`, `.github/`, `Test/`, ...) are excluded from `git archive` and from the build artifact.
- `build/` — all tooling (`package.json`, `.nvmrc`, `.env`, `.husky/`, `dev/` Composer dev dependencies, `scripts/`); **run every `pnpm` command from `build/`**
  - `build/scripts/magento/` — Docker environment + bootstrap (`run.mjs`, `cleanup.mjs`)
  - `build/scripts/php/` — Composer/PHP in Docker
  - `build/scripts/build/` — artifact build
  - `build/scripts/release/` — release checks and release-notes extraction (used by CI)
- `.agents/skills/prepare-release` — skill that prepares a release PR (version bump + `CHANGELOG.md`)

## Commands

All commands run via package scripts; PHP runs inside Docker. Requires Docker and `build/.env` (copy `build/.env.example`, fill `COMMERCE_PUBLIC_KEY` / `COMMERCE_PRIVATE_KEY` from repo.magento.com).

### Development environment
```bash
pnpm magento-minimum            # PHP 8.1 + Magento 2.4.6
pnpm magento-latest             # PHP 8.5 + Magento 2.4.9
pnpm magento-latest:artifact    # same, but mounts the built dist/ artifact (run pnpm build first)
pnpm magento-latest --rebuild-image   # force rebuilding the cached Magento image
pnpm cleanup                    # remove environments (stacks + volumes)
pnpm cleanup --images        # also delete cached livechat-magento:* images
```
The first run per Magento/PHP version builds a ~2 GB image (downloads Magento); later runs reuse it. Admin: `/admin`, user `admin`, password `Admin123!`. The script prints the random port.

### Testing & static analysis
```bash
pnpm test      # PHPUnit (Test/Unit), runs in Docker
pnpm test:magento  # tests under Test/Unit/Controller need real Magento classes: run inside the latest running env (CI job "Magento unit tests" does this on the minimum versions; needs COMMERCE_PUBLIC_KEY / COMMERCE_PRIVATE_KEY secrets)
pnpm phpcs     # Magento2 coding standard; errors fail, warnings are informational
pnpm phpcbf    # auto-fix (rewrites source — review the diff)
```
Config (`phpunit.xml`, `phpcs.xml`) lives next to the module at the repo root and is `export-ignore`d; dev dependencies live in `build/dev/composer.json` (a separate manifest: the module's `autoload.files` entry `registration.php` needs Magento classes, so `vendor/autoload.php` of the module manifest would fatal under PHPUnit/phpcs). The module uses tab indentation, so the Magento2 indentation sniffs are disabled in `phpcs.xml`.

### Composer (runs inside Docker, in `build/dev/`)
```bash
pnpm composer install
pnpm composer require --dev <package>
pnpm composer -d . validate   # the module's own composer.json (repo root)
```

### Build
```bash
pnpm build   # build/dist/livechat-magento/ + build/dist/livechat-magento-<version>.zip (needs the `zip` command)
```
Exclusions come from `export-ignore` in `.gitattributes`. The build only validates the manifest; it does not run `composer install` (Magento dependencies are resolved by the host store).

### Release
```bash
pnpm release:check --base origin/master   # version bumped, tag free, CHANGELOG.md entry without Jira keys
pnpm release:notes --out release-notes.md # bullets of the current version from CHANGELOG.md
pnpm test:release                         # unit tests for the release scripts
```
Release PR branches are named `release/<version>`. Merging one to `master` (merged PR, not a direct push) runs the `Create Release` workflow (tag, build, GitHub release).

## Conventions

- The module is mounted at `app/code/LiveChat/LiveChat` in the dev environment (not a composer symlink — Magento rejects templates outside its root). `run.mjs` bind-mounts only the module's top-level entries (plus `Test/`), generated into `build/scripts/magento/.module.compose.yml`, so `build/` never reaches `app/code`; restart the environment after adding a new top-level file or directory. Only git-tracked files are mounted and packaged, so `git add` new files first.
- Unit tests that need Magento classes require adding `magento/framework` to `build/dev/composer.json` (needs repo.magento.com auth).
- Never commit `build/.env`.
