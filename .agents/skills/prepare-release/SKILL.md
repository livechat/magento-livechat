---
name: prepare-release
description: Prepare a release PR for the Magento plugin - bump the version in composer.json and add a user-facing entry to CHANGELOG.md. Use when asked to "prepare a release", "bump the version", "cut a new version" or "update the changelog for release".
---

# Prepare release

Produces a release PR. CI (`Release PR` workflow) verifies the version was bumped and the changelog has an entry. Merging the PR to `master` triggers the `Create Release` workflow, which tags, builds and publishes the GitHub release. Do not tag or build by hand.

## Steps

1. **Sync.** `git status --porcelain` must be clean. `git fetch origin --tags`, then branch from fresh `origin/master`.
2. **Collect changes.** Last tag: `git describe --tags --abbrev=0 --match 'v*' origin/master`. List what shipped since: `git log <tag>..origin/master --format='%s'` (merge commits and PR titles included). Ignore changes that do not affect the shipped module code (everything not `export-ignore`d in `.gitattributes`) (CI, dev tooling, docs).
3. **Pick the version** (semver `x.y.z`): fixes → patch, new features → minor, breaking changes → major. Propose one with a one-line reason and confirm with the user if it is not obvious.
4. **Create the branch** named `release/<version>` (the release PR check and `Create Release` only run for branches starting with `release/`).
5. **Bump** `"version"` in `composer.json` (the only place the version lives).
6. **Update `CHANGELOG.md`**: insert a `## <version>` section directly under `# Changelog` (newest first) with `- ` bullets:
   - one short, user-facing sentence per change, in imperative mood (e.g. `- Fix cart data not being sent for guest visitors`)
   - no Jira keys / ticket numbers, no PR numbers, no commit-type prefixes (`feat:`, `fix(...)`)
   - skip internal-only changes (CI, tests, refactors with no behavior change)
7. **Verify.** Run `cd build && pnpm release:check --base origin/master`. It must print `✓ Release <version> is ready`; fix whatever it reports.
8. **Commit and open a draft PR** titled `chore: release <version>` (follow the user's commit conventions). In the PR body list the changelog bullets and note that merging triggers the release automatically.

## Rules

- Never mention Jira tasks in the changelog or PR body.
- Do not edit anything other than `composer.json` and `CHANGELOG.md` in a release PR.
