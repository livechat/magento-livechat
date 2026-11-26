import { $, argv, chalk, echo } from "zx";
import { readChangelog, readVersion, rootDir } from "../lib/repo.mjs";
import { compareSemver, isSemver, validateChangelogEntry } from "./lib.mjs";

$.verbose = false;
$.cwd = rootDir;

const version = readVersion();

const problems = [];

if (!isSemver(version ?? "")) {
	problems.push(`composer.json version "${version}" is not x.y.z`);
} else {
	if (argv.base) {
		const baseVersion = JSON.parse((await $`git show ${argv.base}:composer.json`).stdout).version;
		if (compareSemver(version, baseVersion) <= 0) {
			problems.push(
				`composer.json version ${version} was not bumped (${argv.base} has ${baseVersion})`,
			);
		}
	}

	const tags = (await $`git tag --list ${`v${version}`}`).stdout.trim();
	if (tags) {
		problems.push(`git tag v${version} already exists`);
	}

	problems.push(...validateChangelogEntry(readChangelog(), version));
}

if (problems.length > 0) {
	problems.forEach((problem) => echo(chalk.red(`✗ ${problem}`)));
	process.exit(1);
}

echo(chalk.green(`✓ Release ${version} is ready`));
