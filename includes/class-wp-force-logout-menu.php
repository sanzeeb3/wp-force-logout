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
		add_action( 'plugin_action_links_' . plugin_basename( WP_FORCE_LOGOUT_PLUGIN_FILE ), [ $this, 'add_upgrade_link' ] );
	}

	/**
	 * Add WP Force Logout submenu.
	 *
	 * @since 2.0.0
	 */
	public function add_wp_force_logout_submenu() {
		add_users_page(
			'WPForce Logout',
			'<span style="font-size:12px; color:#6bc406">WP Force Logout Pro&nbsp;&nbsp;➤</span>',
			'manage_options',
			'users.php?page=wp-force-logout-pricing'
		);
	}

	/**
	 * Add Upgrade Link in plugin action links.
	 *
	 * @since 2.1.0
	 */
	public function add_upgrade_link( $links ) {

		$links[] = '<a href="users.php?page=wp-force-logout-pricing">' . '<span style="color:#6bc406">Upgrade&nbsp;&nbsp;➤</span>'.'</a>';
		return $links;
	}
}

new WP_Force_Logout_Menu();
