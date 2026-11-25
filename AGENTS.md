# AGENTS.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Module Does

Magento 2 module (`LiveChat_LiveChat`) that integrates LiveChat into a Magento store:
- Connect an existing LiveChat account or create a new one (admin config page)
- Inject the LiveChat JS snippet into the storefront
- Expose visitor and cart data to LiveChat via frontend controllers

## Layout

- `plugin/` — the Magento module (what ships). PSR-4 root `LiveChat\LiveChat\` is `plugin/` itself. `plugin/README.md` is user-facing and ships in the artifact; the root `README.md` is for contributors.
- `dev/` — dev-only Composer tooling (PHPUnit, phpcs + Magento coding standard). Kept separate because the host store supplies the Magento packages; `dev/` holds only test and static-analysis tooling.
- `scripts/magento/` — Docker environment + bootstrap (`run.mjs`, `cleanup.mjs`)
- `scripts/php/` — Composer/PHP in Docker
- `scripts/build/` — artifact build
- `scripts/release/` — release checks and release-notes extraction (used by CI)
- `.agents/skills/prepare-release` — skill that prepares a release PR (version bump + `plugin/CHANGELOG.md`)

## Commands

All commands run via package scripts; PHP runs inside Docker. Requires Docker and `.env` (copy `.env.example`, fill `COMMERCE_PUBLIC_KEY` / `COMMERCE_PRIVATE_KEY` from repo.magento.com).

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
pnpm test      # PHPUnit (plugin/Test/Unit), runs in Docker
pnpm test:magento  # tests under plugin/Test/Unit/Controller need real Magento classes: run inside the latest running env (CI job "Magento unit tests" does this on the minimum versions; needs COMMERCE_PUBLIC_KEY / COMMERCE_PRIVATE_KEY secrets)
pnpm phpcs     # Magento2 coding standard; errors fail, warnings are informational
pnpm phpcbf    # auto-fix (rewrites source — review the diff)
```
Config lives in `dev/` (`phpunit.xml`, `phpcs.xml`). The module uses tab indentation, so the Magento2 indentation sniffs are disabled in `dev/phpcs.xml`.

### Composer (runs inside Docker, in `dev/`)
```bash
pnpm composer install
pnpm composer require --dev <package>
pnpm composer -d plugin validate   # the module's own composer.json
```

### Build
```bash
pnpm build   # dist/livechat-magento/ + dist/livechat-magento-<version>.zip (needs the `zip` command)
```
Exclusions are listed in `plugin/.pluginignore`. The build only validates the manifest; it does not run `composer install` (Magento dependencies are resolved by the host store).

### Release
```bash
pnpm release:check --base origin/master   # version bumped, tag free, CHANGELOG.md entry without Jira keys
pnpm release:notes --out release-notes.md # bullets of the current version from plugin/CHANGELOG.md
pnpm test:release                         # unit tests for the release scripts
```
Release PR branches are named `release/<version>` or `<ticket>--release-<version>`. Merging one to `master` (merged PR, not a direct push) runs the `Create Release` workflow (tag, build, GitHub release).

## Conventions

- The module is mounted at `app/code/LiveChat/LiveChat` in the dev environment (not a composer symlink — Magento rejects templates outside its root).
- Unit tests that need Magento classes require adding `magento/framework` to `dev/composer.json` (needs repo.magento.com auth).
- Never commit `.env`.
