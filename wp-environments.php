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

// Define plugin constants.
define( 'WP_ENVIRONMENTS_PLUGIN_URL', plugins_url( '', __FILE__ ) );

/**
 * Set the default option on activation.
 *
 * This function is hooked into register_activation_hook to run on plugin activation.
 *
 * @link https://developer.wordpress.org/reference/functions/register_activation_hook/
 *
 * @return void
 */
function activation(): void {
	add_option( 'wpe_force_login', 'no' );
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\activation' );

/**
 * Enqueue Stylesheet and add inline styles.
 *
 * This function is hooked into wp_enqueue_scripts and admin_enqueue_scripts to run on every page load.
 *
 * @link https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
 *
 * @return void
 */
function styles(): void {
	// Get the environment setting.
	$environment_type = wp_get_environment_type();

	// Enqueue the main stylesheet.
	wp_enqueue_style( 'wp-environments', WP_ENVIRONMENTS_PLUGIN_URL . '/assets/css/wp-environments.css', array(), filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/wp-environments.css' ) );

	// Define styles for each environment.
	$styles = match ( $environment_type ) {
		'local' => array(
			'--wpe-label'           => '" (Local)"',
			'--wpe-base-color'      => '#1EAA52',
			'--wpe-highlight-color' => '#3CCD49',
		),
		'development' => array(
			'--wpe-label'           => '" (Development)"',
			'--wpe-base-color'      => '#1151FF',
			'--wpe-highlight-color' => '#87CEFF',
		),
		'staging' => array(
			'--wpe-label'           => '" (Staging)"',
			'--wpe-base-color'      => '#5400CC',
			'--wpe-highlight-color' => '#A082FF',
		),
		'production' => array(
			'--wpe-label'           => '" (Production)"',
			'--wpe-base-color'      => '#EE0005',
			'--wpe-highlight-color' => '#FF7975',
		),
		default => array(),
	};

	// Generate and add inline styles.
	if ( ! empty( $styles ) ) {
		$css = ':root {';
		foreach ( $styles as $key => $value ) {
			$css .= "{$key}: {$value};";
		}
		$css .= '}';

		wp_add_inline_style( 'wp-environments', $css );
	}
}

/**
 * Update styles for environment.
 *
 * This function is hooked into init to run on every page load.
 *
 * @link https://developer.wordpress.org/reference/hooks/init/
 *
 * @return void
 */
function update_styles(): void {

	// Bail if user is not logged in.
	if ( ! is_user_logged_in() ) {
		return;
	}

	// Enqueue styles and scripts.
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\styles', 999 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\styles', 999 );
}

add_action( 'init', __NAMESPACE__ . '\\update_styles' );

/**
 * Add a settings page to the admin menu.
 *
 * This function is hooked into admin_menu to run on every page load.
 *
 * @link https://developer.wordpress.org/reference/hooks/admin_menu/
 *
 * @return void
 */
function settings_page(): void {
	add_management_page(
		'WP Environment',
		'Environment',
		'manage_options',
		'wpe_settings',
		__NAMESPACE__ . '\\settings_page_html'
	);
}

add_action( 'admin_menu', __NAMESPACE__ . '\\settings_page' );

/**
 * Render the settings page and persist the force-login option.
 *
 * Registered as the callback for the Tools > Environment page.
 *
 * @return void
 */
function settings_page_html(): void {
	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Flag to determine if settings were saved.
	$show_notice = false;

	// Process the form only after verifying the nonce.
	if ( isset( $_POST['wpe_force_login_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpe_force_login_nonce'] ) ), 'wpe_save_settings' )
	) {
		// If the checkbox is checked, 'yes' is saved; otherwise, 'no' is saved.
		$submitted   = isset( $_POST['wpe_force_login'] ) ? sanitize_text_field( wp_unslash( $_POST['wpe_force_login'] ) ) : 'no';
		$force_login = ( 'yes' === $submitted ) ? 'yes' : 'no';
		update_option( 'wpe_force_login', $force_login );

		// Set a flag to show the admin notice.
		$show_notice = true;
	}

	// Read the current value for rendering the checkbox.
	$force_login = get_option( 'wpe_force_login', 'no' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p>Set the environment type in <code>wp-config.php</code> using the <code>WP_ENVIRONMENT_TYPE</code> constant.
		</p>

		<?php if ( $show_notice ) : ?>
			<div class="updated notice">
				<p><?php esc_html_e( 'Settings saved successfully!', 'wp-environments' ); ?></p>
			</div>
		<?php endif; ?>

		<form action="" method="post">
			<?php wp_nonce_field( 'wpe_save_settings', 'wpe_force_login_nonce' ); ?>
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row">
						Force Login
					</th>
					<td>
						<label for="force_login">
							<input type="checkbox" name="wpe_force_login" id="force_login"
									value="yes" <?php checked( $force_login, 'yes' ); ?>>
							Require users to log in to access the website.
						</label>
					</td>
				</tr>
				</tbody>
			</table>
			<?php submit_button( 'Save Settings' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Force login on all environments if the option is enabled.
 *
 * This function is hooked into template_redirect to run on every page load.
 *
 * @link https://developer.wordpress.org/reference/hooks/template_redirect/
 *
 * @return void
 */
function force_login(): void {
	// If the user is logged in, do nothing.
	if ( is_user_logged_in() ) {
		return;
	}

	// If the setting is enabled, redirect non-logged-in users to the login page.
	if ( 'yes' === get_option( 'wpe_force_login', 'no' ) ) {
		auth_redirect();
	}
}

add_action( 'template_redirect', __NAMESPACE__ . '\\force_login' );

/**
 * Add a link to the plugin's settings page.
 *
 * This function is hooked into plugin_action_links_{plugin_basename} to run on every page load.
 *
 * @link https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_basename/
 *
 * @param array $links Existing plugin action links.
 *
 * @return array
 */
function add_settings_link( $links ): array {
	$settings_link = '<a href="' . esc_url( menu_page_url( 'wpe_settings', false ) ) . '">' . esc_html__( 'Settings', 'wp-environments' ) . '</a>';

	// Add the 'Settings' link at the beginning.
	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __NAMESPACE__ . '\\add_settings_link' );
