# WP Environments

This plugin allows you to set up different environments for your WordPress site. It is useful for when you have a
development site and a production site, and you want to be able to easily distinguish between environments.

## Installation

### Composer (recommended)

The plugin isn't published on Packagist, so add it as a VCS repository in your site's
`composer.json`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/gosuperrad/wp-environments" }
]
```

Then require it:

```bash
composer require superrad/wp-environments:^1.0
```

On [Bedrock](https://roots.io/bedrock/) — or any project that has `composer/installers` —
this installs to `web/app/plugins/wp-environments/` and resolves the latest `v1.x` tag.
Use `dev-main` instead of `^1.0` to track the `main` branch. (Private clones need a GitHub
token in `auth.json` or SSH; this repo is public, so no auth is required.)

### Manual

1. Download `wp-environments.zip` from the [latest release](https://github.com/gosuperrad/wp-environments/releases/latest).
2. In the WordPress admin, go to **Plugins → Add New → Upload Plugin** and upload the zip
   (or unzip it into `wp-content/plugins/`).
3. Activate the plugin through the **Plugins** menu.

### Configure

Set the environment type in `wp-config.php`:

```php
define( 'WP_ENVIRONMENT_TYPE', 'local' ); // local | development | staging | production
```

## Usage

The plugin will add a colored bar to the top of the admin bar. The color of the bar will depend on the environment type.

The default colors are:

1. **Local**:
   - **Color**: Green
   - **Rationale**: Local is usually the starting point for most developers, and green is often associated with safety
     and a starting point. It signifies that changes here don't affect anyone else.

2. **Development**:
   - **Color**: Blue
   - **Rationale**: Blue is calming and denotes that while it's beyond the local environment, it's a place for testing
     and iteration without major consequences.

3. **Staging**:
   - **Color**: Purple
   - **Rationale**: Purple sets staging visibly apart from the other environments. Staging is typically a pre-production
     environment that mirrors production as closely as possible — the last line of defense before going live — so it
     gets its own unmistakable color.

4. **Production**:
   - **Color**: Red
   - **Rationale**: Red typically denotes danger or a need for caution. In the context of web development or software,
     it indicates that any changes or issues here have real-world consequences for users.
