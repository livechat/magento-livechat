import fs from "fs";
import path from "path";
import { $ } from "zx";

export const rootDir = path.resolve(import.meta.dirname, "../../..");

const git = $({ cwd: rootDir, verbose: false });

const topLevel = (file) => file.split("/")[0];

// Tracked and not-ignored untracked files that still exist in the working tree
const listFiles = async () =>
	(await git`git ls-files -z --cached --others --exclude-standard`).stdout
		.split("\0")
		.filter((file) => file && fs.existsSync(path.join(rootDir, file)));

// Top-level paths marked `export-ignore` in .gitattributes
const exportIgnored = async (entries) => {
	const out = (
		await git({ input: entries.join("\0") })`git check-attr -z --stdin export-ignore`
	).stdout.split("\0");
	const ignored = new Set();
	for (let i = 0; i + 2 < out.length; i += 3) {
		if (out[i + 2] === "set") {
			ignored.add(out[i]);
		}
	}
	return ignored;
};

// Files that make up the module, i.e. what `git archive` would ship
export const moduleFiles = async () => {
	const files = await listFiles();
	const ignored = await exportIgnored([...new Set(files.map(topLevel))]);
	return files.filter((file) => !ignored.has(topLevel(file)));
};

export const moduleEntries = async () => [...new Set((await moduleFiles()).map(topLevel))];

export const readVersion = () =>
	JSON.parse(fs.readFileSync(path.join(rootDir, "composer.json"), "utf8")).version;

export const readChangelog = () => fs.readFileSync(path.join(rootDir, "CHANGELOG.md"), "utf8");
