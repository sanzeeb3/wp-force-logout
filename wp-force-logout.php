<?php
/**
 * Plugin Name: WPForce Logout - Login activity, last login and more.
 * Description: Forcefully logout WordPress user(s).
 * Version: 1.0.0
 * Author: Sanjeev Aryal
 * Author URI: http://www.sanjeebaryal.com.np
 * Text Domain: wp-force-logout
 * Domain Path: /languages/
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define WP_FORCE_LOGOUT_PLUGIN_FILE.
if ( ! defined( 'WP_FORCE_LOGOUT_PLUGIN_FILE' ) ) {
	define( 'WP_FORCE_LOGOUT_PLUGIN_FILE', __FILE__ );
}

// Include the main WP_Force_Logout class.
if ( ! class_exists( 'WP_Force_Logout' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wp-force-logout.php';
}

// Initialize the plugin.
add_action( 'plugins_loaded', array( 'WP_Force_Logout', 'get_instance' ) );
