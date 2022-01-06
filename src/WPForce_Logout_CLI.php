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
	public function logout( $users, $assoc_args ) {

		if ( empty( $users ) ) {
			WP_CLI::line( 'Arguments required - "all" or {User Login} or {User ID} or {User Email}. Example: wp wpfl logout 45' );
			return;
		}

		$logout_class = new WP_Force_Logout_Process();

		if ( count( $users ) === 1 && $users[0] === 'all' ) {
			$logout_class->force_all_users_logout();
			WP_CLI::line( 'Logged all users outt!' );
			return;

		} else {

			foreach( $users as $user ) {

				if ( is_email( $user ) ) {
					$user_obj = get_user_by( 'email', $user );
				} elseif ( is_numeric( $user ) ) {
					$user_obj = get_user_by( 'id', $user );
				} else {
					$user_obj = get_user_by( 'login', $user );
				}

				if ( $user_obj ) {
					$sessions = WP_Session_Tokens::get_instance( $user_obj->ID );
					$sessions->destroy_all();

					WP_CLI::line( 'User ' . $user . ' logged outt!' );
				} else {
					WP_CLI::line( 'No user found with passed argument '. $user .'. Are you sure the user exists?' );
				}
			}

		}//end if

		// Just an example of assoc args. If --force assoc exists. For example: wp wpfl logout 34 --force
		if( isset( $assoc_args['force'] ) ) {
			WP_CLI::line( 'Assoc args is not required! Task already donee!' );
		}
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
