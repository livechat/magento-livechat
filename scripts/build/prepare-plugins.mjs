import fs from "fs";
import path from "path";
import { $, chalk, echo } from "zx";

const slug = "livechat-magento";
const distPath = `./dist/${slug}`;

const { version } = JSON.parse(fs.readFileSync("./plugin/composer.json", "utf8"));
if (!version) {
	throw new Error("plugin/composer.json has no version");
}
const zipPath = `./dist/${slug}-${version}.zip`;

// Load exclusion rules from .pluginignore
const ignoreRules = fs
	.readFileSync("./plugin/.pluginignore", "utf8")
	.split("\n")
	.map((line) => line.trim())
	.filter((line) => line && !line.startsWith("#"));

echo`Building ${slug} ${version}...`;

fs.rmSync(distPath, { recursive: true, force: true });
fs.rmSync(zipPath, { force: true });
fs.mkdirSync(distPath, { recursive: true });

fs.cpSync("./plugin", distPath, { recursive: true });

const removeByBasename = (dir, name) => {
	for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
		const full = path.join(dir, entry.name);
		if (entry.name === name) {
			fs.rmSync(full, { recursive: true, force: true });
		} else if (entry.isDirectory()) {
			removeByBasename(full, name);
		}
	}
};

for (const rule of ignoreRules) {
	if (rule.startsWith("**/")) {
		removeByBasename(distPath, rule.slice(3));
	} else {
		fs.rmSync(path.join(distPath, rule), { recursive: true, force: true });
	}
}

// The module's Magento dependencies are resolved by the host store, so only
// the manifest is validated here (no `composer install`).
await $({
	verbose: false,
	env: {
		...process.env,
		DOCKER_UID: process.env.DOCKER_UID ?? process.getuid(),
		DOCKER_GID: process.env.DOCKER_GID ?? process.getgid(),
	},
})`docker compose -f ./scripts/php/docker-compose.yml run --rm --remove-orphans php composer validate --no-check-publish --no-interaction --working-dir=${distPath}`;

try {
	await $`zip --version`.quiet();
} catch {
	throw new Error("The `zip` command is required to build the artifact");
}
// -X drops extra file attributes; the module files sit at the zip root
await $({ cwd: distPath })`zip -q -X -r ${path.resolve(zipPath)} .`;

echo(chalk.green(`Artifact prepared at ${distPath}`));
echo(chalk.green(`Archive: ${zipPath}`));
