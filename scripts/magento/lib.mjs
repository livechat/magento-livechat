import { fs } from "zx";

export const composeFile = "./scripts/magento/docker-compose.yml";
export const projectsFile = "./scripts/magento/.projects";

// Compose project names of the environments started by run.mjs, oldest first
export const readProjects = () =>
	fs.existsSync(projectsFile)
		? fs.readFileSync(projectsFile, "utf8").split("\n").filter(Boolean)
		: [];
