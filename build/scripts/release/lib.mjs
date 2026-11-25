const SEMVER = /^(\d+)\.(\d+)\.(\d+)$/;
const JIRA_KEY = /\b[A-Z][A-Z0-9]+-\d+\b/;

export const isSemver = (version) => SEMVER.test(version);

// Returns >0 when a is newer than b
export const compareSemver = (a, b) => {
	const pa = a.match(SEMVER).slice(1).map(Number);
	const pb = b.match(SEMVER).slice(1).map(Number);
	for (let i = 0; i < 3; i++) {
		if (pa[i] !== pb[i]) {
			return pa[i] - pb[i];
		}
	}
	return 0;
};

// Returns the "- " bullets of the `## <version>` section, or null when the section is missing
export const getChangelogEntry = (changelog, version) => {
	const lines = changelog.split(/\r?\n/);
	const start = lines.findIndex((line) => line.trim() === `## ${version}`);
	if (start === -1) {
		return null;
	}
	const bullets = [];
	for (const line of lines.slice(start + 1)) {
		if (line.startsWith("## ")) {
			break;
		}
		if (line.trim().startsWith("- ")) {
			bullets.push(line.trim());
		}
	}
	return bullets;
};

// Returns a list of human-readable problems with the changelog entry
export const validateChangelogEntry = (changelog, version) => {
	const bullets = getChangelogEntry(changelog, version);
	if (bullets === null) {
		return [`CHANGELOG.md has no "## ${version}" section`];
	}
	if (bullets.length === 0) {
		return [`"## ${version}" in CHANGELOG.md has no "- " bullet points`];
	}
	return bullets
		.filter((bullet) => JIRA_KEY.test(bullet))
		.map((bullet) => `Changelog bullet mentions a Jira task, remove it: ${bullet}`);
};
