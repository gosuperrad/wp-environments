---
name: build-css
description: Recompile the plugin's SCSS to the CSS that WordPress enqueues. Use after editing assets/scss/wp-environments.scss, since the compiled assets/css/wp-environments.css is the source of truth WordPress loads.
---

# Build CSS

`assets/css/wp-environments.css` is what WordPress enqueues; `assets/scss/wp-environments.scss`
is the source. Edits to the SCSS do nothing until recompiled.

The build is an npm pipeline (defined in `package.json`): dart-sass compiles the SCSS,
then PostCSS runs autoprefixer and strips comments. Run from the plugin root:

```bash
npm install      # first time only — installs sass, postcss, autoprefixer, etc.
npm run build
```

`npm run build` runs `build:style` (sass) then `build:postcss` (autoprefixer +
discard-comments) and regenerates `wp-environments.css` and `wp-environments.css.map`.

Notes:
- Do **not** compile with bare `sass` — the committed CSS's compact style comes from
  the PostCSS step. Skipping it produces a different (still valid, but noisy) diff.
- For an active editing session, `npm start` watches the SCSS and rebuilds on save.
  (`npm run watch` is currently broken — it references a missing `broswersync` script.)
- `npm run dist` builds and packages the plugin into `wp-environments.zip`.
- After building, show the user the diff of the compiled `.css` to confirm the change.
