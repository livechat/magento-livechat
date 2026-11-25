import fs from "fs";
import { argv } from "zx";
import { getChangelogEntry } from "./lib.mjs";

const { version } = JSON.parse(fs.readFileSync("./plugin/composer.json", "utf8"));
const bullets = getChangelogEntry(fs.readFileSync("./plugin/CHANGELOG.md", "utf8"), version);

if (!bullets?.length) {
	throw new Error(`plugin/CHANGELOG.md has no entry for ${version}`);
}

// Usage: pnpm release:notes --out <file>
if (!argv.out) {
	throw new Error("--out <file> is required");
}
fs.writeFileSync(argv.out, `${bullets.join("\n")}\n`);
