<?php
/**
 * WP Force Logout PRO File.
 *
 * @package    WP Force Logout
 * @author     Sanjeev Aryal
 * @since      2.0.0
 * @license    GPL-3.0+
 */

class WPForce_Logout_PRO {

	/**
	 * Get Settings.
	 *
	 * @since 2.0.0
	 */
	private $settings = array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->settings = get_option( 'wp_force_logout_settings' );

		add_filter( 'logout_url', array( $this, 'logout_url' ), PHP_INT_MAX, 10, 2 );
		add_action( 'wp_login', array( $this, 'update_login_time' ), 10, 2 );
		add_action( 'wp', array( $this, 'maybe_expire_session' ) );
	}

	/**
	 * Logout URL.
	 *
	 * @since 2.0.0
	 */
	public function logout_url( $logout_url, $redirect ) {

		if ( ! empty( $this->settings['logout_redirect'] ) ) {
			return add_query_arg( 'redirect_to', urlencode( $this->settings['logout_redirect'] ), $logout_url );
		}

		return $logout_url;
	}

	/**
	 * Store login time in usermeta table.
	 *
	 * @since 2.0.0
	 *
	 * @return void.
	 */
	public function update_login_time( $user_login, $user ) {
		update_user_meta( $user->ID, 'login_time', time() );
	}

	/**
	 * Maybe Expire session.
	 *
	 * @since 2.0.0
	 */
	public function maybe_expire_session() {
		if ( ! is_user_logged_in() || empty( $this->settings['session_expiration'] ) ) {
			return;
		}

		$current_user = wp_get_current_user();
		$login_time   = get_user_meta( $current_user->ID, 'login_time', true );

		if ( $login_time && ( time() - strtotime( $login_time ) > 60 * $this->settings['session_expiration'] ) ) {
			// Check if login time is more than the session expiration set.
			wp_logout();
			delete_user_meta( $current_user->ID, 'login_time' );
			// Optional: remove login_time meta
		}
	}
}

new WPForce_Logout_PRO();
