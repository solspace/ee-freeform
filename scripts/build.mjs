/*
 * Freeform for ExpressionEngine - build pipeline
 *
 * One command: `pnpm run build`.
 * Compiles the composer React bundle (esbuild), the control-panel scripts (esbuild),
 * the SCSS (dart-sass + Lightning CSS), copies fonts/themes/datepicker assets, encodes
 * the base64 "crypt" helpers, installs the PHP composer dependencies (if available), and
 * packages everything into dist/EE-Freeform_<version>.zip ready to drop into EE.
 */

import { build as esbuild } from "esbuild";
import * as sassModule from "sass";
import { transform as lightningcss, browserslistToTargets } from "lightningcss";
import browserslist from "browserslist";
import { glob } from "tinyglobby";
import archiver from "archiver";
import { readFile, writeFile, rm, mkdir, cp } from "node:fs/promises";
import { createWriteStream, readFileSync } from "node:fs";
import { dirname, join, relative } from "node:path";
import { execFileSync } from "node:child_process";
import { fileURLToPath } from "node:url";

// Run everything relative to the repo root (this file lives in scripts/).
const ROOT = fileURLToPath(new URL("..", import.meta.url));
process.chdir(ROOT);

// Source and output locations (mirror the old tasks/_paths.js).
const CP_SRC = "src/external/scripts/cp";
const CP_DEST = "src/freeform_next/javascript";
const COMPOSER_ENTRY = "src/external/scripts/composer/app.js";
const COMPOSER_DEST = "src/themes/freeform_next/javascript/composer/app.js";
const STYLES_SRC = "src/external/styles";
const STYLES_DEST = "src/themes/freeform_next/css";
const CRYPT_SRC = "src/crypt";
const CRYPT_DEST = "src/freeform_next/Library/Helpers/Misc";
const FONT_SRC = "src/external/font";
const FONT_DEST = "src/themes/freeform_next/font";
const THEMES_SRC = "src/external/themes";
const THEMES_DEST = "src/themes/freeform_next/lib";
const ROOT_THEMES = "themes/freeform_next";
const ADDON_SETUP = "src/freeform_next/addon.setup.php";

const sass = sassModule;
const cssTargets = browserslistToTargets(browserslist());

const log = (label, start) =>
  console.log(`  ✓ ${label} (${Math.round(performance.now() - start)}ms)`);

async function step(label, fn) {
  const start = performance.now();
  await fn();
  log(label, start);
}

// Remove regenerated output directories. Everything here is rebuilt below; the
// base64 crypt files and PHP vendor dir are overwritten in place instead.
async function clean() {
  await Promise.all(
    [
      CP_DEST,
      STYLES_DEST,
      FONT_DEST,
      THEMES_DEST,
      "src/themes/freeform_next/javascript/composer",
      ROOT_THEMES,
    ].map((dir) => rm(dir, { recursive: true, force: true, maxRetries: 3, retryDelay: 100 }))
  );
}

// React form builder -> single IIFE bundle. React 15 classic JSX runtime; the CP
// injects ~15 free globals (csrfToken, formId, ...) that resolve to window at runtime.
async function buildReact() {
  await esbuild({
    entryPoints: [COMPOSER_ENTRY],
    outfile: COMPOSER_DEST,
    bundle: true,
    format: "iife",
    target: "es2015",
    jsx: "transform",
    jsxFactory: "React.createElement",
    jsxFragment: "React.Fragment",
    loader: { ".js": "jsx" },
    minify: true,
    legalComments: "none",
    logLevel: "warning",
  });
}

// Standalone control-panel scripts. These are small, hand-maintained browser scripts
// (jQuery, no modules) served directly by the CP - copy them verbatim so the checked-in
// output stays readable and editable. Only the React app bundle gets minified.
async function buildScripts() {
  await cp(CP_SRC, CP_DEST, { recursive: true });
}

// SCSS -> dart-sass (compile) -> Lightning CSS (autoprefix + minify).
async function buildStyles() {
  const files = await glob(`${STYLES_SRC}/**/*.scss`, { ignore: ["**/_*.scss"] });
  await Promise.all(
    files.map(async (file) => {
      const out = join(STYLES_DEST, relative(STYLES_SRC, file).replace(/\.scss$/, ".css"));
      const { css } = sass.compile(file, {
        loadPaths: [STYLES_SRC],
        silenceDeprecations: [
          "import",
          "slash-div",
          "global-builtin",
          "legacy-js-api",
          "color-functions",
        ],
      });
      const { code } = lightningcss({
        filename: out,
        code: Buffer.from(css),
        minify: true,
        targets: cssTargets,
      });
      await mkdir(dirname(out), { recursive: true });
      await writeFile(out, code);
    })
  );
}

// Base64-encode each crypt source, write with the extension stripped.
async function buildCrypt() {
  const files = await glob(`${CRYPT_SRC}/**/*`);
  await Promise.all(
    files.map(async (file) => {
      const out = join(CRYPT_DEST, relative(CRYPT_SRC, file).replace(/\.[^/.]+$/, ""));
      const buffer = await readFile(file);
      await mkdir(dirname(out), { recursive: true });
      await writeFile(out, buffer.toString("base64"));
    })
  );
}

async function buildFonts() {
  await cp(FONT_SRC, FONT_DEST, { recursive: true });
}

async function buildThemes() {
  await cp(THEMES_SRC, THEMES_DEST, { recursive: true });
}

// Copy the hand-maintained datepicker/flatpickr assets into the root themes dir.
// Runs after buildScripts (javascript/fields) and buildStyles (css/fields).
async function buildDatepicker() {
  await mkdir(`${ROOT_THEMES}/javascript/fields`, { recursive: true });
  await cp(
    `${CP_DEST}/fields/datepicker.js`,
    `${ROOT_THEMES}/javascript/fields/datepicker.js`
  );
  await cp(
    `${CP_DEST}/fields/flatpickr.js`,
    `${ROOT_THEMES}/javascript/fields/flatpickr.js`
  );
  await mkdir(`${ROOT_THEMES}/css/fields`, { recursive: true });
  await cp(
    `${STYLES_DEST}/fields/datepicker.css`,
    `${ROOT_THEMES}/css/fields/datepicker.css`
  );
}

// Install PHP dependencies if composer is available; otherwise package the
// already-committed vendor/ directory.
function composer() {
  try {
    execFileSync(
      "composer",
      [
        "install",
        "--working-dir=src/freeform_next",
        "--optimize-autoloader",
        "--no-dev",
        "--no-interaction",
      ],
      { stdio: "ignore" }
    );
  } catch (error) {
    const reason = error.code === "ENOENT" ? "composer not installed" : "composer install failed";
    console.log(`  ! ${reason}; packaging the committed vendor/ instead`);
  }
}

function readVersion() {
  const setup = readFileSync(ADDON_SETUP, "utf8");
  const match = setup.match(/['"]version['"]\s*=>\s*['"]([0-9a-z.\-]+)['"]/);
  if (!match) throw new Error("Could not read version from addon.setup.php");
  return match[1];
}

function zipDirectory(sourceDir, outFile) {
  return new Promise((resolve, reject) => {
    const output = createWriteStream(outFile);
    const archive = archiver("zip", { zlib: { level: 9 } });
    output.on("close", resolve);
    archive.on("error", reject);
    archive.pipe(output);
    archive.directory(sourceDir, false);
    archive.finalize();
  });
}

// Assemble the addon + themes into a staging dir, prune vendor tests/docs,
// and zip to dist/EE-Freeform_<version>.zip.
async function pack() {
  const version = readVersion();
  const buildDir = "dist/build";
  await rm(buildDir, { recursive: true, force: true, maxRetries: 3, retryDelay: 100 });
  await mkdir(buildDir, { recursive: true });

  await cp("src/freeform_next", join(buildDir, "freeform_next"), { recursive: true });
  await cp("src/themes", join(buildDir, "themes"), { recursive: true });
  await cp(ROOT_THEMES, join(buildDir, "themes/freeform_next"), { recursive: true });

  // Ship as the full "Freeform" edition (no-op if the source already says so).
  const setupPath = join(buildDir, "freeform_next/addon.setup.php");
  const setup = await readFile(setupPath, "utf8");
  await writeFile(
    setupPath,
    setup.replace(/(['"]name['"]\s*=>\s*['"])Freeform Lite(['"],)/g, "$1Freeform$2")
  );

  const junkDirs = await glob(
    `${buildDir}/freeform_next/vendor/**/{tests,Tests,test,doc}`,
    { onlyDirectories: true }
  );
  await Promise.all(junkDirs.map((dir) => rm(dir, { recursive: true, force: true, maxRetries: 3, retryDelay: 100 })));

  // Drop macOS cruft so it never ships in the addon.
  const dsStores = await glob(`${buildDir}/**/.DS_Store`, { dot: true });
  await Promise.all(dsStores.map((file) => rm(file, { force: true })));

  await mkdir("dist", { recursive: true });
  const zipPath = `dist/EE-Freeform_${version}.zip`;
  await zipDirectory(buildDir, zipPath);
  await rm(buildDir, { recursive: true, force: true, maxRetries: 3, retryDelay: 100 });
  return zipPath;
}

async function main() {
  const total = performance.now();
  console.log("Building Freeform for EE...");

  await step("clean", clean);
  await Promise.all([
    step("react bundle", buildReact),
    step("cp scripts", buildScripts),
    step("styles", buildStyles),
    step("crypt", buildCrypt),
    step("fonts", buildFonts),
    step("themes", buildThemes),
    step("composer", async () => composer()),
  ]);
  await step("datepicker assets", buildDatepicker);
  let zipPath;
  await step("package zip", async () => {
    zipPath = await pack();
  });

  console.log(
    `\nDone in ${Math.round(performance.now() - total)}ms -> ${zipPath}`
  );
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
