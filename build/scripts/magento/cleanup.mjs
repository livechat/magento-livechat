import { $, chalk, echo, fs, minimist } from "zx";
import { composeFile, projectsFile, readProjects } from "./lib.mjs";

const { project, images } = minimist(process.argv.slice(2), {
	string: ["project"],
	boolean: ["images"],
});

const projects = readProjects();
const toRemove = project ? projects.filter((p) => p === project) : projects;

for (const name of toRemove) {
	echo`Cleaning up project: ${name}`;

	await $`docker compose -f ${composeFile} -p ${name} down -v --remove-orphans`;
}

const remaining = projects.filter((p) => !toRemove.includes(p));
if (!remaining.length) {
	fs.rmSync(projectsFile, { force: true });
} else {
	fs.writeFileSync(projectsFile, remaining.join("\n") + "\n");
}

if (images) {
	const tags = (
		await $`docker image ls livechat-magento --format {{.Repository}}:{{.Tag}}`
	).stdout
		.split("\n")
		.filter(Boolean);
	if (tags.length) {
		await $`docker rmi ${tags}`;
	}
}

echo(chalk.green`Cleanup completed`);
