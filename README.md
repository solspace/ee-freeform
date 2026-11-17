# Solspace: Freeform (ExpressionEngine)

## Requirements

- **Node 24** (Node 22 also works). If you use `nvm`: `nvm use` (reads `.nvmrc`).
- **pnpm** (`corepack enable` or install globally).

## Building

Install dependencies once, then build:

```sh
pnpm install
pnpm run build
```

Salesforce authentication regression checks run without live credentials:

```sh
php scripts/check-salesforce.php
```

`pnpm run build` does everything in one pass:

- Bundles the React form builder with **esbuild** → `src/themes/freeform_next/javascript/composer/app.js`
- Copies the standalone control-panel scripts verbatim (kept readable, not minified) → `src/freeform_next/javascript/`
- Compiles SCSS with **dart-sass** and prefixes/minifies with **Lightning CSS** → `src/themes/freeform_next/css/`
- Copies fonts, themes and the datepicker assets, and base64-encodes the "crypt" helpers
- Installs the PHP Composer dependencies (if `composer` is available; otherwise the
  committed `vendor/` is packaged as-is)
- Packages the full addon into **`dist/EE-Freeform_<version>.zip`** — the file you drop into EE

The version in the zip name is read from `src/freeform_next/addon.setup.php`.

## Build toolchain

The build is a single Node script, `scripts/build.mjs` — no Gulp, Babel or Browserify. Everything
runs on current tooling: esbuild (JS), dart-sass + Lightning CSS (styles), and Node's own
`fs`/`child_process` for copying, encoding and zipping.

> Note: the React builder UI still targets React 15; the build compiles it as-is. esbuild's
> minimum output target is ES2015, so IE11 is not supported.

## Composer / PHP

`pnpm run build` runs `composer install` for you when possible. Some Composer libraries differ
between PHP 7 and PHP 8, so if you need to refresh `vendor/` manually run Composer against the
appropriate PHP version, e.g.:

```sh
/opt/homebrew/opt/php@8.3/bin/php $(which composer) update --working-dir=src/freeform_next
```
