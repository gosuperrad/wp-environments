# WP Environments

This plugin allows you to set up different environments for your WordPress site. It is useful for when you have a
development site and a production site, and you want to be able to easily distinguish between environments.

## Installation

1. Upload the `wp-environments` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Set the environment type in wp-config.php using the WP_ENVIRONMENT_TYPE constant. For
   example: `define( 'WP_ENVIRONMENT_TYPE', 'local' );`

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
   - **Color**: Yellow or Orange
   - **Rationale**: Yellow or orange is often used as a signal for caution. Staging is typically a pre-production
     environment that mirrors production as closely as possible. It's the last line of defense before going live, so
     extra caution is needed.

4. **Production**:
   - **Color**: Red
   - **Rationale**: Red typically denotes danger or a need for caution. In the context of web development or software,
     it indicates that any changes or issues here have real-world consequences for users.
