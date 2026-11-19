import { $, fs } from "zx";

// Runs the Magento-dependent unit tests inside the most recently started environment (pnpm magento-latest / magento-minimum)
const projects = fs.existsSync("./scripts/magento/.projects")
	? fs.readFileSync("./scripts/magento/.projects", "utf8").split("\n").filter(Boolean)
	: [];
const project = projects.at(-1);
if (!project) {
	throw new Error("No Magento environment found. Run: pnpm magento-latest");
}

const args = process.argv.slice(3);
await $({ verbose: false, stdio: "inherit" })`docker compose -f ./scripts/magento/docker-compose.yml -p ${project} exec -T -u 33:33 -w /var/www/html php vendor/bin/phpunit --bootstrap vendor/autoload.php --colors=always ${args} app/code/LiveChat/LiveChat/Test/Unit/Controller`;
