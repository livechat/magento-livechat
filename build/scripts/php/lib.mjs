import { $ } from "zx";

const phpVersion = process.env.npm_package_config_php_minimum_ver ?? "8.1";

// Runs composer in the PHP tooling container; `workingDir` is relative to the repo root (mounted at /home/www-data)
export const composer = (args, workingDir = "build/dev") =>
	$({
		verbose: false,
		env: {
			...process.env,
			PHP_VERSION: phpVersion,
			DOCKER_UID: process.env.DOCKER_UID ?? process.getuid(),
			DOCKER_GID: process.env.DOCKER_GID ?? process.getgid(),
		},
	})`docker compose -f ./scripts/php/docker-compose.yml run --rm --remove-orphans php composer --working-dir=${workingDir} ${args}`.pipe(
		process.stdout,
	);
