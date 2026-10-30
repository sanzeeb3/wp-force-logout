<?php
/**
 * WP Force Logout PRO File.
 *
 * @package    WP Force Logout
 * @author     Sanjeev Aryal
 * @since      2.0.0
 * @license    GPL-3.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}

class WPForce_Logout_PRO {

	/**
	 * Cron hook of the scheduled logout.
	 *
	 * @since 3.0.0
	 */
	const SCHEDULED_LOGOUT_HOOK = 'wp_force_logout_scheduled_logout';

	/**
	 * Get Settings.
	 *
	 * @since 2.0.0
	 */
	private $settings = array();

	/**
	 * Session token of the login in progress, used by the single session feature.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private $login_token = '';

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->settings = (array) get_option( 'wp_force_logout_settings', array() );

		add_filter( 'logout_url', array( $this, 'logout_url' ), PHP_INT_MAX, 2 );
		add_filter( 'auth_cookie_expiration', array( $this, 'auth_cookie_expiration' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_wp_force_logout_maybe_logout_idle_users', array( $this, 'maybe_logout_idle_users' ) );
		add_action( 'wp_ajax_wp_force_logout_maybe_logout_on_browser_closure', array( $this, 'maybe_logout_on_browser_closure' ) );

		add_action( 'set_logged_in_cookie', array( $this, 'remember_login_token' ), 10, 6 );
		add_action( 'wp_login', array( $this, 'enforce_single_session' ), 10, 2 );
		add_action( 'after_password_reset', array( $this, 'logout_on_password_change' ) );
		add_action( 'profile_update', array( $this, 'logout_on_profile_password_change' ), 10, 2 );

		add_action( 'init', array( $this, 'maybe_schedule_logout' ) );
		add_action( 'wp_force_logout_settings_saved', array( $this, 'reschedule_logout' ) );
		add_action( self::SCHEDULED_LOGOUT_HOOK, array( $this, 'scheduled_logout' ) );
	}

	/**
	 * Whether a checkbox setting is on.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key Setting key.
	 *
	 * @return bool
	 */
	private function is_on( $key ) {
		return ! empty( $this->settings[ $key ] ) && 'on' === $this->settings[ $key ];
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
		$minutes = ! empty( $this->settings['session_expiration'] ) ? absint( $this->settings['session_expiration'] ) : 0;

		return $minutes ? MINUTE_IN_SECONDS * $minutes : $expiration;
	}

	/**
	 * Whether idle logout applies to the given user, see the "Roles to logout when idle" setting.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_User $user User.
	 *
	 * @return bool
	 */
	private function idle_logout_applies( $user ) {

		if ( ! $this->is_on( 'idle_logout' ) || ! $user->exists() ) {
			return false;
		}

		$roles = ! empty( $this->settings['idle_logout_roles'] ) ? (array) $this->settings['idle_logout_roles'] : array();

		return empty( $roles ) || (bool) array_intersect( $roles, (array) $user->roles );
	}

	/**
	 * Enqueue the idle and browser close logout script for logged in users.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_scripts() {

		if ( ! is_user_logged_in() ) {
			return;
		}

		$idle_logout          = $this->idle_logout_applies( wp_get_current_user() );
		$browser_close_logout = $this->is_on( 'browser_close_logout' );

		if ( ! $idle_logout && ! $browser_close_logout ) {
			return;
		}

		$idle_minutes = ! empty( $this->settings['idle_logout_period'] ) ? absint( $this->settings['idle_logout_period'] ) : 30;

		wp_enqueue_script( 'wp-force-logout-pro', plugins_url( 'assets/js/pro/logout.js', WP_FORCE_LOGOUT_PLUGIN_FILE ), array(), WPFL_VERSION, true );
		wp_localize_script(
			'wp-force-logout-pro',
			'wpfl_pro_params',
			array(
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'nonce'                => wp_create_nonce( 'wp-force-logout-pro' ),
				'idle_logout'          => $idle_logout,
				'idle_timeout'         => MINUTE_IN_SECONDS * 1000 * max( 1, $idle_minutes ),
				'browser_close_logout' => $browser_close_logout,
			)
		);
	}

	/**
	 * Maybe logout user on browser closure.
	 *
	 * @since 2.0.0
	 */
	public function maybe_logout_on_browser_closure() {

		check_ajax_referer( 'wp-force-logout-pro', 'security' );

		if ( $this->is_on( 'browser_close_logout' ) ) {
			wp_logout();
		}

		wp_die();
	}

	/**
	 * Maybe logout idle users.
	 *
	 * @since 2.0.0
	 */
	public function maybe_logout_idle_users() {

		check_ajax_referer( 'wp-force-logout-pro', 'security' );

		if ( $this->idle_logout_applies( wp_get_current_user() ) ) {
			wp_logout();
		}

		wp_die();
	}

	/**
	 * Remember the session token of the login in progress, wp_login doesn't pass it.
	 *
	 * @since 3.0.0
	 */
	public function remember_login_token( $logged_in_cookie, $expire, $expiration, $user_id, $scheme, $token ) {
		$this->login_token = (string) $token;
	}

	/**
	 * Enforce single active session per user.
	 *
	 * @since 2.1.0
	 */
	public function enforce_single_session( $user_login, $user ) {

		if ( ! $this->is_on( 'single_session' ) || ! $user instanceof WP_User || '' === $this->login_token ) {
			return;
		}

		WP_Session_Tokens::get_instance( $user->ID )->destroy_others( $this->login_token );
	}

	/**
	 * Logout all sessions on password reset.
	 *
	 * @since 2.1.0
	 */
	public function logout_on_password_change( $user ) {

		if ( ! $this->is_on( 'password_change' ) || ! $user instanceof WP_User ) {
			return;
		}

		WP_Session_Tokens::get_instance( $user->ID )->destroy_all();
	}

	/**
	 * Logout all sessions when the password is changed from a profile screen.
	 *
	 * Users changing their own password stay logged in on the current device, core re-issues its cookie with the same session token.
	 *
	 * @since 3.0.0
	 *
	 * @param int     $user_id       User ID.
	 * @param WP_User $old_user_data User before the update.
	 */
	public function logout_on_profile_password_change( $user_id, $old_user_data ) {

		$user = get_userdata( $user_id );

		if ( ! $this->is_on( 'password_change' ) || ! $user || ! $old_user_data instanceof WP_User || $user->user_pass === $old_user_data->user_pass ) {
			return;
		}

		$sessions = WP_Session_Tokens::get_instance( $user_id );
		$token    = wp_get_session_token();

		if ( get_current_user_id() === $user_id && $token ) {
			$sessions->destroy_others( $token );
		} else {
			$sessions->destroy_all();
		}
	}

	/**
	 * Schedule the logout if it's enabled but not scheduled, e.g. after an update to 3.0.0.
	 *
	 * @since 3.0.0
	 */
	public function maybe_schedule_logout() {

		if ( $this->is_on( 'scheduled_logout' ) && ! wp_next_scheduled( self::SCHEDULED_LOGOUT_HOOK ) ) {
			$this->schedule_next_logout();
		}
	}

	/**
	 * Reschedule the logout after the settings are saved.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings Saved settings.
	 */
	public function reschedule_logout( $settings ) {

		$this->settings = (array) $settings;

		wp_clear_scheduled_hook( self::SCHEDULED_LOGOUT_HOOK );

		if ( $this->is_on( 'scheduled_logout' ) ) {
			$this->schedule_next_logout();
		}
	}

	/**
	 * Logout all users, then schedule the next run.
	 *
	 * @since 3.0.0
	 */
	public function scheduled_logout() {

		if ( ! $this->is_on( 'scheduled_logout' ) ) {
			return;
		}

		WP_Session_Tokens::destroy_all_for_all_users();

		$this->schedule_next_logout();
	}

	/**
	 * Schedule the next logout at the configured site time.
	 *
	 * Single events instead of a daily recurrence, so the time stays put across DST changes.
	 *
	 * @since 3.0.0
	 */
	private function schedule_next_logout() {

		$time                  = ! empty( $this->settings['scheduled_logout_time'] ) ? $this->settings['scheduled_logout_time'] : '00:00';
		list( $hour, $minute ) = array_map( 'intval', explode( ':', $time ) );

		$now  = new DateTimeImmutable( 'now', wp_timezone() );
		$next = $now->setTime( $hour, $minute );

		if ( $next <= $now ) {
			$next = $next->modify( '+1 day' );
		}

		wp_schedule_single_event( $next->getTimestamp(), self::SCHEDULED_LOGOUT_HOOK );
	}
}

new WPForce_Logout_PRO();
