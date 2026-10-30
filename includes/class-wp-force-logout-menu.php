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
 * WP Force Logout Menu Class. Upgrade links for users without a PRO license; PRO adds its settings page itself.
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

		add_action( 'current_screen', array( $this, 'fill_freemius_page_title' ) );

		if ( ! wpfl_fs()->is_not_paying() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_upgrade_submenu' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( WP_FORCE_LOGOUT_PLUGIN_FILE ), array( $this, 'add_upgrade_link' ) );
	}

	/**
	 * Give Freemius pages without a menu item a title.
	 *
	 * Freemius registers its opt-in, license activation and other pages with an empty parent when
	 * there is no visible menu item for them, e.g. before the PRO license is activated. WordPress
	 * can't resolve a title for those, and admin-header.php then passes null to strip_tags() (#44).
	 *
	 * @since 4.0.0
	 */
	public function fill_freemius_page_title() {
		global $title, $plugin_page;

		if ( empty( $plugin_page ) || ! preg_match( '/^wp-force-logout(-|$)/', $plugin_page ) ) {
			return;
		}

		if ( null === get_admin_page_title() ) {
			$title = 'WPForce Logout'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Only set when WordPress found none.
		}
	}

	/**
	 * Add the upgrade link under the Users menu.
	 *
	 * @since 2.0.0
	 */
	public function add_upgrade_submenu() {
		add_users_page(
			'WPForce Logout',
			'<span style="font-size:12px; color:#6bc406">WP Force Logout Pro&nbsp;&nbsp;➤</span>',
			'manage_options',
			wpfl_fs()->get_upgrade_url()
		);
	}

	/**
	 * Add Upgrade Link in plugin action links.
	 *
	 * @since 2.1.0
	 */
	public function add_upgrade_link( $links ) {

		$links[] = '<a href="' . esc_url( wpfl_fs()->get_upgrade_url() ) . '"><span style="color:#6bc406">' . esc_html__( 'Upgrade', 'wp-force-logout' ) . '&nbsp;&nbsp;➤</span></a>';
		return $links;
	}
}

new WP_Force_Logout_Menu();
