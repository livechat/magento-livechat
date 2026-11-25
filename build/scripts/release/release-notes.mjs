import fs from "fs";
import path from "path";
import { rootDir } from "../lib/repo.mjs";
import { argv } from "zx";
import { getChangelogEntry } from "./lib.mjs";

const { version } = JSON.parse(fs.readFileSync(path.join(rootDir, "composer.json"), "utf8"));
const bullets = getChangelogEntry(fs.readFileSync(path.join(rootDir, "CHANGELOG.md"), "utf8"), version);

if (!bullets?.length) {
	throw new Error(`CHANGELOG.md has no entry for ${version}`);
}

// Usage: pnpm release:notes --out <file>
if (!argv.out) {
	throw new Error("--out <file> is required");
}
fs.writeFileSync(argv.out, `${bullets.join("\n")}\n`);
