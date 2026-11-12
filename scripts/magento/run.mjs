import crypto from "crypto";
import { $, chalk, dotenv, echo, fs, minimist } from "zx";

const MIN_MAGENTO = "2.4.6";
const MIN_PHP = "8.1";

const {
	php: phpVersion,
	magento: magentoVersion,
	port: portArg,
	dbPort: dbPortArg,
	"rebuild-image": rebuildImage,
} = minimist(process.argv.slice(2), {
	string: ["php", "magento", "port", "dbPort"],
	boolean: ["rebuild-image"],
});

const parse = (v) => v.split(".").map((n) => parseInt(n, 10) || 0);
const compare = (a, b) => {
	const [x, y] = [parse(a), parse(b)];
	for (let i = 0; i < Math.max(x.length, y.length); i++) {
		if ((x[i] ?? 0) !== (y[i] ?? 0)) return (x[i] ?? 0) - (y[i] ?? 0);
	}
	return 0;
};

if (!phpVersion || !magentoVersion) {
	throw new Error("Usage: run.mjs --php <version> --magento <version>");
}
if (compare(phpVersion, MIN_PHP) < 0) {
	throw new Error(`PHP ${MIN_PHP} or higher is required`);
}
if (compare(magentoVersion, MIN_MAGENTO) < 0) {
	throw new Error(`Magento ${MIN_MAGENTO} or higher is required`);
}

/** ----- COMPOSER CREDENTIALS (repo.magento.com) ----- */
const envFile = fs.existsSync("./.env")
	? dotenv.parse(fs.readFileSync("./.env"))
	: {};
const publicKey = process.env.COMMERCE_PUBLIC_KEY ?? envFile.COMMERCE_PUBLIC_KEY;
const privateKey =
	process.env.COMMERCE_PRIVATE_KEY ?? envFile.COMMERCE_PRIVATE_KEY;
if (!publicKey || !privateKey) {
	throw new Error(
		"COMMERCE_PUBLIC_KEY and COMMERCE_PRIVATE_KEY are required (set them in .env, see .env.example)",
	);
}
const composerAuth = JSON.stringify({
	"http-basic": {
		"repo.magento.com": { username: publicKey, password: privateKey },
	},
});

const port = portArg ?? Math.floor(Math.random() * (8190 - 8090 + 1)) + 8090;
const dbPort = dbPortArg ?? Math.floor(Math.random() * (8390 - 8290 + 1)) + 8290;
const p = `magento_${crypto.randomBytes(3).toString("hex").slice(0, 5)}`;
const f = "./scripts/magento/docker-compose.yml";
// Magento < 2.4.8 does not support OpenSearch 2.19
const opensearchImage =
	compare(magentoVersion, "2.4.8") >= 0
		? "opensearchproject/opensearch:2.19.1"
		: "opensearchproject/opensearch:2.5.0";

const admin = { user: "admin", password: "Admin123!", email: "admin@admin.com" };

echo`Preparing the Magento instance for\n* PHP version: ${phpVersion}\n* Magento version: ${magentoVersion}\n* OpenSearch: ${opensearchImage}\n* Port: ${port}\n* Docker project: ${p}\n`;

fs.appendFileSync("./scripts/magento/.projects", p + "\n");

$.log = (entry) => {
	if (entry.kind === "cmd") {
		console.log(chalk.bold("Running: ") + entry.cmd);
	}
};

const $$ = $({
	verbose: false,
	env: {
		...process.env,
		MAGENTO_PORT: port,
		DB_PORT: dbPort,
		PHP_VERSION: phpVersion,
		MAGENTO_VERSION: magentoVersion,
		OPENSEARCH_IMAGE: opensearchImage,
		COMPOSER_AUTH: composerAuth,
	},
});

const exec = (...cmd) =>
	$$`docker compose -f ${f} -p ${p} exec -T php ${cmd}`;
const magento = (...args) => exec("php", "-d", "memory_limit=-1", "bin/magento", ...args);

/** ----- MAGENTO IMAGE (built once per Magento + PHP version, reused afterwards) ----- */
const imageTag = `livechat-magento:${magentoVersion}-php${phpVersion}`;
const imageExists =
	(await $$({ nothrow: true })`docker image inspect ${imageTag}`).exitCode === 0;
if (rebuildImage || !imageExists) {
	echo(chalk.bold(`Building ${imageTag} (downloads Magento, slow only once)...`));
	await $$`docker compose -f ${f} -p ${p} build php`;
} else {
	echo(chalk.bold(`Reusing image ${imageTag}`));
}

/** ----- RUNNING STACK (waits for db + opensearch healthchecks) ----- */
await $$`docker compose -f ${f} -p ${p} up -d --wait`;

/** ----- INSTALLING MAGENTO ----- */
await magento(
	"setup:install",
	`--base-url=http://localhost:${port}/`,
	"--db-host=db",
	"--db-name=magento",
	"--db-user=root",
	"--db-password=root",
	"--admin-firstname=Admin",
	"--admin-lastname=Admin",
	`--admin-email=${admin.email}`,
	`--admin-user=${admin.user}`,
	`--admin-password=${admin.password}`,
	"--language=en_US",
	"--currency=USD",
	"--timezone=UTC",
	"--use-rewrites=1",
	"--search-engine=opensearch",
	"--opensearch-host=opensearch",
	"--opensearch-port=9200",
	"--backend-frontname=admin",
	"--cleanup-database",
);

/** ----- DEV SETUP ----- */
const status = (await magento("module:status")).stdout;
const twoFactorModules = status
	.split("\n")
	.map((l) => l.trim())
	.filter((l) => /^Magento_(AdminAdobeIms)?TwoFactorAuth$/.test(l));
if (twoFactorModules.length) {
	await magento("module:disable", ...twoFactorModules);
}
await magento("module:enable", "LiveChat_LiveChat");
await magento("setup:upgrade");
await magento("deploy:mode:set", "developer", "--skip-compilation");
await magento("cache:flush");

/** ----- MAGENTO IS READY ----- */
echo(chalk.green(`Magento storefront: http://localhost:${port}/`));
echo(
	chalk.green(
		`Magento admin: http://localhost:${port}/admin (${admin.user} / ${admin.password})`,
	),
);
echo(chalk.green(`Database: localhost:${dbPort} (root / root, db: magento)`));
echo(`Docker project: ${p}`);
echo(`Port: ${port}`);
