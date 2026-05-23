<?php
/**
 * Uninstall routine — remove the plugin's options.
 *
 * Runs only when the plugin is deleted from the WordPress admin.
 *
 * @package SuperRad\WP_Environments
 */

// Exit if WordPress is not performing an uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'wpe_force_login' );
