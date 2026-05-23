---
name: release
description: Cut a new plugin release — bump the version across the plugin header and readme.txt, update the changelog, then commit and tag. User-triggered only.
disable-model-invocation: true
---

# Release

Usage: `/release <version>` (e.g. `/release 2.1.0`). The version in `$ARGUMENTS` is
the new SemVer version to ship.

The plugin's version currently lives in three files that disagree (`wp-environments.php`
header says `2.0.1`, `readme.txt` Stable tag and `package.json` both say `1.0.0`). A
release reconciles all of them to the new version.

## Steps

1. **Validate input.** If `$ARGUMENTS` is empty or not a SemVer string (`X.Y.Z`),
   ask the user for the version before doing anything.

2. **Bump the version** in all three files:
   - `wp-environments.php` — the `Version:` plugin header.
   - `readme.txt` — the `Stable tag:` line.
   - `package.json` — the `version` field.
   Also consider bumping `Tested up to:` in `readme.txt` if WordPress has moved on —
   ask the user rather than guessing.

3. **Update the changelog.** Add a `<version>` entry under `== Changelog ==` in
   `readme.txt`. Draft the bullet points from `git log` / the diff since the last
   tag, then confirm the summary with the user.

4. **Review.** Show the user the full diff and get explicit confirmation before
   committing.

5. **Commit and tag** (only after confirmation):
   ```bash
   git commit -am "Release v<version>"
   git tag "v<version>"
   ```

6. **Do not push automatically.** Tell the user to push when ready:
   `git push && git push --tags` (remote `origin` is GitHub —
   `gosuperrad/wp-environments`; `gh` is available for a release there). Pushing is an
   outward-facing action — leave it to the user unless they ask you to push.
