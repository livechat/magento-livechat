import fs from "fs";
import { argv } from "zx";
import { readChangelog, readVersion } from "../lib/repo.mjs";
import { getChangelogEntry } from "./lib.mjs";

const version = readVersion();
const bullets = getChangelogEntry(readChangelog(), version);

if (!bullets?.length) {
	throw new Error(`CHANGELOG.md has no entry for ${version}`);
}

// Usage: pnpm release:notes --out <file>
if (!argv.out) {
	throw new Error("--out <file> is required");
}
fs.writeFileSync(argv.out, `${bullets.join("\n")}\n`);
