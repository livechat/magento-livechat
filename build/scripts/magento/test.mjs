import { $ } from "zx";
import { composeFile, readProjects } from "./lib.mjs";

// Runs the Magento-dependent unit tests inside the most recently started environment (pnpm magento-latest / magento-minimum)
const project = readProjects().at(-1);
if (!project) {
	throw new Error("No Magento environment found. Run: pnpm magento-latest");
}

const args = process.argv.slice(3);
await $({ verbose: false, stdio: "inherit" })`docker compose -f ${composeFile} -p ${project} exec -T -u 33:33 -w /var/www/html php vendor/bin/phpunit --bootstrap vendor/autoload.php --colors=always ${args} app/code/LiveChat/LiveChat/Test/Unit/Controller`;
