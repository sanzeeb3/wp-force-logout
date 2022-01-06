<?php
/**
 * WP Force Logout CLI File.
 *
 * @package    WP Force Logout
 * @author     Sanjeev Aryal
 * @since      1.5.0
 * @license    GPL-3.0+
 */

class WPForce_Logout_CLI {

	/**
	 * Force logout given user.
	 *
	 * @since  1.5.0
	 */
	public function logout() {

		$logout_class = new WP_Force_Logout_Process();
		$logout_class->force_all_users_logout();

		WP_CLI::line( 'Logged all users outt!' );
	}
}

/**
 * Registers our command when cli get's initialized.
 *
 * @since  1.5.0
 */
function wpforce_logout_cli_register_commands() {
	WP_CLI::add_command( 'wpfl', 'WPForce_Logout_CLI' );
}

add_action( 'cli_init', 'wpforce_logout_cli_register_commands' );
