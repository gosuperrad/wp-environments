<?php
/**
 * PHPUnit bootstrap: stub the WordPress functions the plugin touches, then load it.
 *
 * These are unit tests — they do not boot WordPress. We define just enough of the WP
 * API (as global-namespace fallbacks) for the plugin file to include cleanly and for
 * the functions under test to run. Namespaced calls in the plugin fall back to these
 * global definitions.
 *
 * @package SuperRad\WP_Environments
 */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Satisfy the "direct access" guard so including the plugin does not exit().
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

// Registration/hook calls invoked at include time — no-ops here.
if ( ! function_exists( 'add_action' ) ) {
	function add_action( ...$args ) {
		return true;
	}
}
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( ...$args ) {
		return true;
	}
}
if ( ! function_exists( 'register_activation_hook' ) ) {
	function register_activation_hook( ...$args ) {
		return null;
	}
}
if ( ! function_exists( 'plugins_url' ) ) {
	function plugins_url( $path = '', $plugin = '' ) {
		return $path;
	}
}
if ( ! function_exists( 'plugin_basename' ) ) {
	function plugin_basename( $file ) {
		return basename( $file );
	}
}

// Helpers used by the functions under test.
if ( ! function_exists( 'menu_page_url' ) ) {
	function menu_page_url( $menu_slug, $display = true ) {
		$url = 'tools.php?page=' . $menu_slug;
		if ( $display ) {
			echo $url; // phpcs:ignore
		}
		return $url;
	}
}
if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $url ) {
		return $url;
	}
}
if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text, $domain = 'default' ) {
		return $text;
	}
}

// Load the plugin under test.
require_once dirname( __DIR__ ) . '/wp-environments.php';
