# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A standalone WordPress plugin (`SuperRad\WP_Environments`) that color-codes the
admin bar per environment — local / development / staging / production — based on
`wp_get_environment_type()`. It also offers an optional "force login" setting.

It physically lives inside a Roots/Bedrock monorepo at `web/app/plugins/`, but it
is **its own git repo**: default branch is `main`, and the live remote is GitHub —
`git@github.com:gosuperrad/wp-environments.git`. Treat it as a self-contained plugin,
not as part of the surrounding `backend/` project.

## Code style — WordPress Coding Standards

Follow WPCS, not PSR-12 (see `.editorconfig`):
- **Tabs** for indentation.
- Spaces inside parentheses: `if ( ! is_user_logged_in() ) {`.
- Functions are namespaced under `SuperRad\WP_Environments` and registered with
  `__NAMESPACE__ . '\\name'`.

**Do NOT run Laravel Pint on this plugin.** The surrounding Bedrock backend
(`backend/`) uses Pint (PSR-12), which would reformat this code away from WPCS.

## Styles: SCSS is the source of truth

`assets/scss/wp-environments.scss` is the source; `assets/css/wp-environments.css`
is what WordPress actually enqueues (cache-busted with `filemtime`). **Editing the
`.scss` has no effect until it is recompiled** — run `npm run build` (or `/build-css`)
after any SCSS change. The build is an npm pipeline: dart-sass then PostCSS
(autoprefixer + discard-comments); don't compile with bare `sass`, as the committed
CSS's compact style comes from the PostCSS step. `npm start` watches and rebuilds on
save. The per-environment colors are CSS custom properties injected inline from PHP
(`styles()` in `wp-environments.php`); the SCSS only consumes those variables.

## Versioning

The version lives in **three files that must stay in sync**: the `Version:` header in
`wp-environments.php`, `Stable tag:` in `readme.txt`, and `version` in `package.json`
(currently all `2.0.1`). Use `/release <version>` to bump all three plus the changelog.

## Checks

- `composer lint` / `composer lint:fix` — PHPCS against WordPress Coding Standards
  (ruleset in `phpcs.xml.dist`; `tests/`, `vendor/`, `node_modules/` excluded).
- `composer test` — PHPUnit unit tests in `tests/`, bootstrapped with lightweight WP
  function stubs (`tests/bootstrap.php`); they don't boot a real WordPress.
