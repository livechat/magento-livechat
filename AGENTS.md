# AGENTS.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Module Does

Magento 2 module (`LiveChat_LiveChat`) that integrates LiveChat into a Magento store:
- Connect an existing LiveChat account or create a new one (admin config page)
- Inject the LiveChat JS snippet into the storefront
- Expose visitor and cart data to LiveChat via frontend controllers

## Layout

- `plugin/` — the Magento module (what ships). PSR-4 root `LiveChat\LiveChat\` is `plugin/` itself.
- `dev/` — dev-only Composer tooling (PHPUnit, phpcs + Magento coding standard). Kept separate because `plugin/composer.json` requires `magento/*` packages.
- `scripts/magento/` — Docker environment + bootstrap (`run.mjs`, `cleanup.mjs`)
- `scripts/php/` — Composer/PHP in Docker
- `scripts/build/` — artifact build

## Commands

All commands run via npm scripts; PHP runs inside Docker. Requires Docker and `.env` (copy `.env.example`, fill `COMMERCE_PUBLIC_KEY` / `COMMERCE_PRIVATE_KEY` from repo.magento.com).

### Development environment
```bash
npm run magento-minimum            # PHP 8.1 + Magento 2.4.6
npm run magento-latest             # PHP 8.4 + Magento 2.4.8
npm run magento-latest:artifact    # same, but mounts the built dist/ artifact (run npm run build first)
npm run magento-latest -- --rebuild-image   # force rebuilding the cached Magento image
npm run cleanup                    # remove environments (stacks + volumes)
npm run cleanup -- --images        # also delete cached livechat-magento:* images
```
The first run per Magento/PHP version builds a ~2 GB image (downloads Magento); later runs reuse it. Admin: `/admin`, user `admin`, password `Admin123!`. The script prints the random port.

### Testing & static analysis
```bash
npm run test      # PHPUnit (plugin/Test/Unit), runs in Docker
npm run phpcs     # Magento2 coding standard; errors fail, warnings are informational
npm run phpcbf    # auto-fix (rewrites source — review the diff)
```
Config lives in `dev/` (`phpunit.xml`, `phpcs.xml`). The module uses tab indentation, so the Magento2 indentation sniffs are disabled in `dev/phpcs.xml`.

### Composer (runs inside Docker, in `dev/`)
```bash
npm run composer -- install
npm run composer -- require --dev <package>
npm run composer -- -d plugin validate   # the module's own composer.json
```

### Build
```bash
npm run build   # dist/livechat-magento/ + dist/livechat-magento-<version>.zip (needs the `zip` command)
```
Exclusions are listed in `plugin/.pluginignore`. The build only validates the manifest; it does not run `composer install` (Magento dependencies are resolved by the host store).

## Conventions

- The module is mounted at `app/code/LiveChat/LiveChat` in the dev environment (not a composer symlink — Magento rejects templates outside its root).
- Unit tests that need Magento classes require adding `magento/framework` to `dev/composer.json` (needs repo.magento.com auth).
- Never commit `.env`.
