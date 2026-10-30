<?php
/**
 * Plugin Name: WPForce Logout
 * Description: Forcefully logout WordPress user(s), see who's online, last login activity & more.
 * Version: 3.0.0
 * Requires at least: 5.3
 * Requires PHP: 7.0
 * Author: Mini Plugins
 * Author URI: https://miniplugins.com/
 * Text Domain: wp-force-logout
 * Domain Path: /languages/
 * License: GPL-3.0+
 *
 * @fs_premium_only /src/Pro/, /assets/js/pro/
 *
 * @package    WP Force Logout
 * @author     Mini Plugins
 * @since      1.0.0
 * @license    GPL-3.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}

if ( function_exists( 'wpfl_fs' ) ) {
	wpfl_fs()->set_basename( true, __FILE__ );
} else {

	if ( ! function_exists( 'wpfl_fs' ) ) {
		// Create a helper function for easy SDK access.
		function wpfl_fs() {
			global $wpfl_fs;

			if ( ! isset( $wpfl_fs ) ) {
				// Include Freemius SDK.
				if ( file_exists( __DIR__ . '/vendor/freemius/start.php' ) ) {
					require_once __DIR__ . '/vendor/freemius/start.php';
				} else {
					require_once __DIR__ . '/freemius/start.php';
				}

				$wpfl_fs = fs_dynamic_init(
					array(
						'id'             => '15307',
						'slug'           => 'wp-force-logout',
						'type'           => 'plugin',
						'public_key'     => 'pk_0f5e34fac8223c01f054f8692b748',
						'is_premium'     => true,
						'has_addons'     => false,
						'has_paid_plans' => true,
						'menu'           => array(
							'first-path' => 'users.php',
							'contact'    => false,
							'support'    => false,
							'pricing'    => false,
							'parent'     => array(
								'slug' => 'users.php',
							),
						),
					)
				);
			}

			return $wpfl_fs;
		}

		// Init Freemius.
		wpfl_fs();
		// Signal that SDK was initiated.
		do_action( 'wpfl_fs_loaded' );
	}

	/**
	 * Clean up on uninstall. Freemius needs this hook instead of uninstall.php to report the uninstall.
	 *
	 * Settings and last login data are kept, so switching between the free and PRO version doesn't lose them.
	 *
	 * @since 3.0.0
	 */
	function wpfl_fs_uninstall_cleanup() {
		delete_option( 'wpfl_review_notice_dismissed' );
		delete_option( 'wpfl_upgrade_notice_dismissed' );
		delete_transient( 'online_status' );
		wp_clear_scheduled_hook( 'wp_force_logout_scheduled_logout' );
	}

	wpfl_fs()->add_action( 'after_uninstall', 'wpfl_fs_uninstall_cleanup' );

	register_deactivation_hook(
		__FILE__,
		function () {
			wp_clear_scheduled_hook( 'wp_force_logout_scheduled_logout' );
		}
	);

	// Define WP_FORCE_LOGOUT_PLUGIN_FILE.
	if ( ! defined( 'WP_FORCE_LOGOUT_PLUGIN_FILE' ) ) {
		define( 'WP_FORCE_LOGOUT_PLUGIN_FILE', __FILE__ );
	}

	// Include the main WP_Force_Logout class.
	if ( ! class_exists( 'WP_Force_Logout' ) ) {
		include_once __DIR__ . '/includes/class-wp-force-logout.php';
	}

	// Initialize the plugin.
	add_action( 'plugins_loaded', array( 'WP_Force_Logout', 'get_instance' ) );
}
