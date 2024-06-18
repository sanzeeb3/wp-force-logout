<?php
/**
 * Plugin Name: WPForce Logout
 * Description: Forcefully logout WordPress user(s), see who's online, last login activity & more.
 * Version: 2.1.0
 * Author: Mini Plugins
 * Author URI: https://miniplugins.com/
 * Text Domain: wp-force-logout
 * Domain Path: /languages/
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

if ( ! function_exists( 'wpfl_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wpfl_fs() {
        global $wpfl_fs;

        if ( ! isset( $wpfl_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $wpfl_fs = fs_dynamic_init( array(
                'id'                  => '15307',
                'slug'                => 'wp-force-logout',
                'type'                => 'plugin',
                'public_key'          => 'pk_0f5e34fac8223c01f054f8692b748',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'first-path'     => 'users.php',
                    'contact'        => false,
                    'support'        => false
                ),
            ) );
        }

        return $wpfl_fs;
    }

    // Init Freemius.
    wpfl_fs();
    // Signal that SDK was initiated.
    do_action( 'wpfl_fs_loaded' );
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
