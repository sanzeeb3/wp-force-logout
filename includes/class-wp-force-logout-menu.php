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

		/**
		 * Maybe add the menu in the free version that redirects to the Pro version pricing page.
		 * Let's not do this now.
		 */

		add_action( 'admin_menu', [ $this, 'add_wp_force_logout_submenu' ] );
	}

	/**
	 * Add WP Force Logout submenu.
	 *
	 * @since 2.0.0
	 */
	public function add_wp_force_logout_submenu() {
		add_users_page(
			'WPForce Logout', // page title
			'<span style="font-size:10px;" class="fs-submenu-item fs-sub wp-force-logout pricing upgrade-mode">WP Force Logout Pro&nbsp;&nbsp;➤</span>',
			'manage_options', // capability
			'wp-force-logout-pro', // menu slug
			[ $this, 'render' ]
		);
	}

	/**
	 * WPForce Logout page render.
	 *
	 * @since 2.0.0
	 */
	public function render() {
		wp_safe_redirect( admin_url( 'users.php?page=wp-force-logout-pricing' ) );
		exit();
	}
}

new WP_Force_Logout_Menu();
