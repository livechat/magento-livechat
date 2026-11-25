import assert from "node:assert/strict";
import { test } from "node:test";
import { compareSemver, getChangelogEntry, isSemver, validateChangelogEntry } from "./lib.mjs";

const changelog = `# Changelog

## 2.5.0
- Add feature
- Fix bug

## 2.4.12
- Old change
`;

test("isSemver accepts only x.y.z", () => {
	assert.ok(isSemver("2.4.12"));
	assert.ok(!isSemver("2.4"));
	assert.ok(!isSemver("v2.4.12"));
});

test("compareSemver orders numerically", () => {
	assert.ok(compareSemver("2.4.12", "2.4.9") > 0);
	assert.ok(compareSemver("2.5.0", "2.4.12") > 0);
	assert.ok(compareSemver("2.4.12", "2.4.12") === 0);
	assert.ok(compareSemver("2.4.11", "2.4.12") < 0);
});

test("getChangelogEntry returns only the requested section", () => {
	assert.deepEqual(getChangelogEntry(changelog, "2.5.0"), ["- Add feature", "- Fix bug"]);
	assert.equal(getChangelogEntry(changelog, "9.9.9"), null);
});

test("validateChangelogEntry passes for a valid entry", () => {
	assert.deepEqual(validateChangelogEntry(changelog, "2.5.0"), []);
});

test("validateChangelogEntry fails for missing section", () => {
	assert.equal(validateChangelogEntry(changelog, "2.6.0").length, 1);
});

test("validateChangelogEntry fails for empty section", () => {
	assert.equal(validateChangelogEntry("## 2.6.0\n\n## 2.5.0\n- x\n", "2.6.0").length, 1);
});

test("validateChangelogEntry rejects Jira keys", () => {
	const problems = validateChangelogEntry("## 2.6.0\n- SEL-123 fix thing\n", "2.6.0");
	assert.equal(problems.length, 1);
});
