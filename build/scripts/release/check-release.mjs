import fs from "fs";
import path from "path";
import { rootDir } from "../lib/repo.mjs";
import { $, argv, echo, chalk } from "zx";
import { compareSemver, isSemver, validateChangelogEntry } from "./lib.mjs";

$.verbose = false;
$.cwd = rootDir;

const readVersion = (json) => JSON.parse(json).version;

const version = readVersion(fs.readFileSync(path.join(rootDir, "composer.json"), "utf8"));

const problems = [];

if (!isSemver(version ?? "")) {
	problems.push(`composer.json version "${version}" is not x.y.z`);
} else {
	if (argv.base) {
		const baseVersion = readVersion((await $`git show ${argv.base}:composer.json`).stdout);
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

	problems.push(...validateChangelogEntry(fs.readFileSync(path.join(rootDir, "CHANGELOG.md"), "utf8"), version));
}

if (problems.length > 0) {
	problems.forEach((problem) => echo(chalk.red(`✗ ${problem}`)));
	process.exit(1);
}

echo(chalk.green(`✓ Release ${version} is ready`));
