import fs from "fs";
import path from "path";
import { $, chalk, echo } from "zx";
import { composer } from "../php/lib.mjs";
import { moduleFiles, readVersion, rootDir } from "../lib/repo.mjs";

const slug = "livechat-magento";
const distPath = path.join(rootDir, "build/dist", slug);

const version = readVersion();
if (!version) {
	throw new Error("composer.json has no version");
}
const zipPath = path.join(rootDir, "build/dist", `${slug}-${version}.zip`);

echo`Building ${slug} ${version}...`;

fs.rmSync(distPath, { recursive: true, force: true });
fs.rmSync(zipPath, { force: true });

// Same file set as `git archive`: everything except the `export-ignore` paths from .gitattributes
for (const file of await moduleFiles()) {
	const target = path.join(distPath, file);
	fs.mkdirSync(path.dirname(target), { recursive: true });
	fs.copyFileSync(path.join(rootDir, file), target);
}

// The module's Magento dependencies are resolved by the host store, so only
// the manifest is validated here (no `composer install`).
await composer(
	["validate", "--no-check-publish", "--no-interaction"],
	path.relative(rootDir, distPath),
);

// -X drops extra file attributes; the module files sit at the zip root
await $({ cwd: distPath })`zip -q -X -r ${zipPath} .`;

echo(chalk.green(`Artifact prepared at ${distPath}`));
echo(chalk.green(`Archive: ${zipPath}`));
