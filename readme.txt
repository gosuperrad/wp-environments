=== WP Environments ===
Contributors: Super Rad
Tags: environments, development, production, staging
Donate link: https://paypal.me/superraddev
Requires at least: 5.7
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily distinguish between various environments with a color-coded background and label in the WordPress admin bar.

== Description ==
Have you ever been scared of making changes because you thought you were on your production site, but you were actually in your staging environment? WP Environments comes to the rescue! This plugin allows you to set an environment type for your WordPress instance and display a clear and color-coded label in the admin bar, ensuring you always know where you are.

* Set environment type via wp-config.php.
* Automatic detection and preference for the WP_ENVIRONMENT_TYPE constant if set in wp-config.php.
* Supports both single WordPress installations and multisite networks.
* Four predefined environment types with color codes: Local, Development, Staging, Production.
* Optional "Force Login" setting to require authentication before the site is viewable.

== Installation ==
1. Upload the `wp-environments` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Set the environment type in wp-config.php using the WP_ENVIRONMENT_TYPE constant. For example: `define( 'WP_ENVIRONMENT_TYPE', 'local' );`

== Screenshots ==
1. Color-coded admin bar for "Local".
2. Color-coded admin bar for "Development".
3. Color-coded admin bar for "Staging".
4. Color-coded admin bar for "Production".

== Changelog ==

= 2.0.1 =
* Restrict environment styling to logged-in users.
* Inject per-environment colors as CSS custom properties (Local, Development, Staging, Production).
* Add an optional "Force Login" setting under Tools > Environment.

= 1.0.0 =
* Initial release.
