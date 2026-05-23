<?php
/**
 * Plugin Name: WP Environments
 * Plugin URI: https://github.com/gosuperrad/wp-environments
 * Description: Add a color coded background and label to the admin bar to distinguish between environments.
 * Author: Super Rad⚡️
 * Author URI: https://gosuperrad.com/?utm_source=wp-environments&utm_medium=website&utm_campaign=plugins-page
 * Text Domain: wp-environments
 * Version: 2.0.1
 * Requires PHP: 8.0
 * Requires at least: 5.7
 * Update URI: https://github.com/gosuperrad/wp-environments
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package SuperRad\WP_Environments
 *
 * WP Environments, (C) 2024 Super Rad LLC.
 * WP Environments is distributed under the terms of the GNU GPL.
 */

namespace SuperRad\WP_Environments;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WP_ENVIRONMENTS_PLUGIN_URL', plugins_url( '', __FILE__ ) );

const FORCE_LOGIN_OPTION = 'wpe_force_login';
const SETTINGS_GROUP     = 'wpe_settings';
const SETTINGS_PAGE      = 'wpe_settings';

/**
 * Per-environment CSS custom property values, keyed by environment type.
 *
 * Returns an empty array for unrecognized environments so the plugin can leave
 * unconfigured sites untouched.
 *
 * @param string $environment_type The value from wp_get_environment_type().
 *
 * @return array<string, string>
 */
function environment_variables( string $environment_type ): array {
	return match ( $environment_type ) {
		'local'       => array(
			'--wpe-label'           => '" (Local)"',
			'--wpe-base-color'      => '#1EAA52',
			'--wpe-highlight-color' => '#3CCD49',
		),
		'development' => array(
			'--wpe-label'           => '" (Development)"',
			'--wpe-base-color'      => '#1151FF',
			'--wpe-highlight-color' => '#87CEFF',
		),
		'staging'     => array(
			'--wpe-label'           => '" (Staging)"',
			'--wpe-base-color'      => '#5400CC',
			'--wpe-highlight-color' => '#A082FF',
		),
		'production'  => array(
			'--wpe-label'           => '" (Production)"',
			'--wpe-base-color'      => '#EE0005',
			'--wpe-highlight-color' => '#FF7975',
		),
		default       => array(),
	};
}

/**
 * Set the default option on activation.
 *
 * @link https://developer.wordpress.org/reference/functions/register_activation_hook/
 *
 * @return void
 */
function activation(): void {
	add_option( FORCE_LOGIN_OPTION, 'no' );
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\activation' );

/**
 * Load the plugin text domain.
 *
 * @return void
 */
function load_textdomain(): void {
	load_plugin_textdomain( 'wp-environments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', __NAMESPACE__ . '\\load_textdomain' );

/**
 * Enqueue the stylesheet and inject the per-environment CSS variables.
 *
 * Bails for unrecognized environments, so a site without WP_ENVIRONMENT_TYPE set
 * is left completely untouched.
 *
 * @link https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
 *
 * @return void
 */
function styles(): void {
	$variables = environment_variables( wp_get_environment_type() );

	if ( empty( $variables ) ) {
		return;
	}

	$css_path = plugin_dir_path( __FILE__ ) . 'assets/css/wp-environments.css';

	wp_enqueue_style(
		'wp-environments',
		WP_ENVIRONMENTS_PLUGIN_URL . '/assets/css/wp-environments.css',
		// Depend on the active admin color scheme so our accent overrides print
		// after it and win on source order. The 'colors' handle is only enqueued
		// in the admin, so leave dependencies empty on the front end.
		is_admin() ? array( 'colors' ) : array(),
		(string) filemtime( $css_path )
	);

	$declarations = '';
	foreach ( $variables as $property => $value ) {
		$declarations .= "{$property}: {$value};";
	}

	wp_add_inline_style( 'wp-environments', ":root { {$declarations} }" );
}

/**
 * Hook the environment styles for logged-in users only.
 *
 * @link https://developer.wordpress.org/reference/hooks/init/
 *
 * @return void
 */
function enqueue_styles(): void {
	if ( ! is_user_logged_in() ) {
		return;
	}

	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\styles', 999 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\styles', 999 );
}

add_action( 'init', __NAMESPACE__ . '\\enqueue_styles' );

/**
 * Register the Tools > Environment settings page.
 *
 * @link https://developer.wordpress.org/reference/hooks/admin_menu/
 *
 * @return void
 */
function settings_page(): void {
	add_management_page(
		__( 'WP Environment', 'wp-environments' ),
		__( 'Environment', 'wp-environments' ),
		'manage_options',
		SETTINGS_PAGE,
		__NAMESPACE__ . '\\render_settings_page'
	);
}

add_action( 'admin_menu', __NAMESPACE__ . '\\settings_page' );

/**
 * Register the force-login setting, section, and field via the Settings API.
 *
 * @link https://developer.wordpress.org/reference/hooks/admin_init/
 *
 * @return void
 */
function register_settings(): void {
	register_setting(
		SETTINGS_GROUP,
		FORCE_LOGIN_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => __NAMESPACE__ . '\\sanitize_force_login',
			'default'           => 'no',
		)
	);

	add_settings_section( 'wpe_general', '', '__return_false', SETTINGS_PAGE );

	add_settings_field(
		FORCE_LOGIN_OPTION,
		__( 'Force Login', 'wp-environments' ),
		__NAMESPACE__ . '\\render_force_login_field',
		SETTINGS_PAGE,
		'wpe_general'
	);
}

add_action( 'admin_init', __NAMESPACE__ . '\\register_settings' );

/**
 * Sanitize the force-login option to a strict 'yes' or 'no'.
 *
 * @param mixed $value The submitted value.
 *
 * @return string
 */
function sanitize_force_login( $value ): string {
	return ( 'yes' === $value ) ? 'yes' : 'no';
}

/**
 * Render the force-login checkbox field.
 *
 * The leading hidden input guarantees a value is submitted when the box is
 * unchecked (the checkbox value wins when checked).
 *
 * @return void
 */
function render_force_login_field(): void {
	$force_login = get_option( FORCE_LOGIN_OPTION, 'no' );
	?>
	<input type="hidden" name="<?php echo esc_attr( FORCE_LOGIN_OPTION ); ?>" value="no">
	<label>
		<input type="checkbox" name="<?php echo esc_attr( FORCE_LOGIN_OPTION ); ?>" value="yes" <?php checked( $force_login, 'yes' ); ?>>
		<?php esc_html_e( 'Require users to log in to access the website.', 'wp-environments' ); ?>
	</label>
	<?php
}

/**
 * Render the settings page.
 *
 * @return void
 */
function render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- options.php verified the nonce on save; this only shows a confirmation notice.
	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error( SETTINGS_GROUP, 'wpe_saved', __( 'Settings saved.', 'wp-environments' ), 'success' );
	}

	settings_errors( SETTINGS_GROUP );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p>Set the environment type in <code>wp-config.php</code> using the <code>WP_ENVIRONMENT_TYPE</code> constant.</p>
		<form action="options.php" method="post">
			<?php
			settings_fields( SETTINGS_GROUP );
			do_settings_sections( SETTINGS_PAGE );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Force login on all environments if the option is enabled.
 *
 * @link https://developer.wordpress.org/reference/hooks/template_redirect/
 *
 * @return void
 */
function force_login(): void {
	if ( is_user_logged_in() ) {
		return;
	}

	if ( 'yes' === get_option( FORCE_LOGIN_OPTION, 'no' ) ) {
		auth_redirect();
	}
}

add_action( 'template_redirect', __NAMESPACE__ . '\\force_login' );

/**
 * Add a "Settings" link to the plugin's row on the Plugins screen.
 *
 * @link https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_basename/
 *
 * @param array $links Existing plugin action links.
 *
 * @return array
 */
function add_settings_link( $links ): array {
	$settings_link = '<a href="' . esc_url( menu_page_url( SETTINGS_PAGE, false ) ) . '">' . esc_html__( 'Settings', 'wp-environments' ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __NAMESPACE__ . '\\add_settings_link' );
