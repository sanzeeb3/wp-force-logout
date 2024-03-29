<?php
/**
 * WP Force Logout Menu File.
 *
 * @package    WP Force Logout
 * @author     Sanjeev Aryal
 * @since      1.0.0
 * @license    GPL-3.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}

/**
 * WP Force Logout Menu Class.
 *
 * @class WP_Force_Logout_Menu
 *
 * @since  1.0.0
 */
class WP_Force_Logout_Menu {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_wp_force_logout_submenu' ] );
	}

	/**
	 * Add WP Force Logout submenu.
	 *
	 * @since 2.0.0
	 */
	public function add_wp_force_logout_submenu() {
		add_submenu_page(
			'users.php', // parent slug
			'WPForce Logout', // page title
			'WPForce Logout', // menu title
			'manage_options', // capability
			'wp-force-logout-pricing', // menu slug
		);
	}
}

new WP_Force_Logout_Menu();
