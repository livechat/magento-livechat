import { $ } from "zx";

const args = process.argv.slice(3);

const phpVersion = process.env.npm_package_config_php_minimum_ver ?? "8.1";

await $({
	verbose: false,
	env: {
		...process.env,
		PHP_VERSION: phpVersion,
		DOCKER_UID: process.env.DOCKER_UID ?? process.getuid(),
		DOCKER_GID: process.env.DOCKER_GID ?? process.getgid(),
	},
})`docker compose -f ./scripts/php/docker-compose.yml run --rm --remove-orphans php composer ${args}`.pipe(
	process.stdout,
);
