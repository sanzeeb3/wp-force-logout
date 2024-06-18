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
		add_filter( 'auth_cookie_expiration', [ $this, 'auth_cookie_expiration' ] );
		add_action( 'wp_ajax_wp_force_logout_maybe_logout_idle_users', array( $this, 'maybe_logout_idle_users' ) );
		add_action( 'wp_ajax_nopriv_wp_force_logout_maybe_logout_idle_users', array( $this, 'maybe_logout_idle_users' ) );

		if ( ! empty( $this->settings['browser_close_logout'] ) ) {
			// Auto logout on browser close isn't working for some reasons.
		}
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
	 * Set auth cookie based on settings.
	 *
	 * @since 2.0.0
	 *
	 * @return int Expiration time.
	 */
	public function auth_cookie_expiration( $expiration ) {
		return ! empty( $this->settings['session_expiration'] ) ? 60 * $this->settings['session_expiration'] : $expiration;
	}

	/**
	 * Maybe logout user on browser closure.
	 *
	 * @since 2.0.0
	 */
	public function maybe_logout_on_browser_closure() {

		check_admin_referer( 'review-notice', 'security' );

		if ( is_user_logged_in() && ! empty( $this->settings['browser_close_logout'] ) && 'on' === $this->settings['browser_close_logout'] ) {
			wp_logout();
		}
	}

	/**
	 * Maybe logout idle users.
	 *
	 * @since 2.0.0
	 */
	public function maybe_logout_idle_users() {

		check_admin_referer( 'review-notice', 'security' );

		if ( is_user_logged_in() && ! empty( $this->settings['idle_logout'] ) && 'on' === $this->settings['idle_logout'] ) {
			wp_logout();
		}
	}
}

new WPForce_Logout_PRO();
