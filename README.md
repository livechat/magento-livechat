# Magento LiveChat

Repository of the `LiveChat_LiveChat` Magento 2 module. The module itself lives in [`plugin/`](plugin/); for what it does and how to install it as a user, see [plugin/README.md](plugin/README.md).

This document covers developing, testing and building the module. Deeper notes for contributors and agents are in [AGENTS.md](AGENTS.md).

## Repository layout

* `plugin/` - the Magento module (what ships)
* `dev/` - dev-only Composer tooling (PHPUnit, phpcs + Magento coding standard)
* `scripts/` - Docker environments (`magento/`), Composer in Docker (`php/`), artifact build (`build/`)

## Prerequisites

* Docker
* Node.js (version in `.nvmrc`) and pnpm
* repo.magento.com keys (Magento environments only)

## Setup

```bash
cp .env.example .env    # fill COMMERCE_PUBLIC_KEY / COMMERCE_PRIVATE_KEY
pnpm install
```

## Development environment

```bash
pnpm magento-minimum                  # PHP 8.1 + Magento 2.4.6
pnpm magento-latest                   # PHP 8.5 + Magento 2.4.9
pnpm magento-latest:artifact          # same, but mounts the built dist/ artifact (run pnpm build first)
pnpm magento-latest --rebuild-image   # force rebuilding the cached Magento image
pnpm cleanup                          # remove environments (stacks + volumes)
pnpm cleanup --images                 # also delete cached livechat-magento:* images
```

The first run per Magento/PHP version builds a ~2 GB image; later runs reuse it. The script prints the storefront URL (random port). Admin: `/admin`, user `admin`, password `Admin123!`.

## Testing and static analysis

```bash
pnpm test           # PHPUnit in Docker
pnpm test:magento   # controller tests that need real Magento classes, run inside a running environment
pnpm phpcs          # Magento2 coding standard
pnpm phpcbf         # auto-fix (review the diff)
```

A pre-push hook runs `pnpm phpcs && pnpm test`.

## Build

```bash
pnpm build   # dist/livechat-magento/ + dist/livechat-magento-<version>.zip (needs `zip`)
```

Exclusions are listed in `plugin/.pluginignore`.

## Releasing

Releases are cut from `master` in two automated steps:

1. **Release PR** - run the `prepare-release` skill ([`.agents/skills/prepare-release`](.agents/skills/prepare-release/SKILL.md)) or do it by hand: bump `version` in `plugin/composer.json`, add a `## <version>` section with bullet points to `plugin/CHANGELOG.md` (no Jira keys), and open a PR from a `release/<version>` (or `<ticket>--release-<version>`) branch. The `Release PR` workflow fails unless the version was bumped and the changelog entry exists. Check locally with `pnpm release:check --base origin/master`.
2. **Create Release** - merging the PR (only a `release/*` or `*--release-*` branch) triggers the `Create Release` workflow: it tags `v<version>`, builds `livechat-magento-<version>.zip` and publishes a GitHub release with the changelog bullets as notes. It can also be re-run from Actions -> Create Release.

## CI

GitHub Actions runs `test`, `phpcs` and `magento-tests` (Magento unit tests on the minimum versions). The last one needs the repository secrets `COMMERCE_PUBLIC_KEY` and `COMMERCE_PRIVATE_KEY` and is skipped for PRs from forks.
