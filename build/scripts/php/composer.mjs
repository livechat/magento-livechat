import { composer } from "./lib.mjs";

// zx puts the script path at argv[2], so forwarded arguments start at 3
await composer(process.argv.slice(3));
