<?php
/**
 * WP Force Logout PRO Active Sessions File.
 *
 * @package    WP Force Logout
 * @author     Sanjeev Aryal
 * @since      4.0.0
 * @license    GPL-3.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}

/**
 * Lists a user's active sessions on the profile screen and logs out individual ones.
 *
 * Core only offers "Log Out Everywhere". Sessions are stored by hashed token, which the public
 * WP_Session_Tokens API can't destroy individually, so this reads and writes the session_tokens
 * user meta of the default session manager.
 *
 * @since 4.0.0
 */
class WPForce_Logout_Sessions {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'render' ), 5 );
		add_action( 'edit_user_profile', array( $this, 'render' ), 5 );
		add_action( 'admin_post_wpfl_destroy_session', array( $this, 'destroy_session' ) );
		add_action( 'admin_notices', array( $this, 'notice' ) );
	}

	/**
	 * Whether sessions are stored in user meta, i.e. no plugin replaced the session manager.
	 *
	 * @return bool
	 */
	private function is_supported() {
		return 'WP_User_Meta_Session_Tokens' === apply_filters( 'session_token_manager', 'WP_User_Meta_Session_Tokens' );
	}

	/**
	 * Unexpired sessions of a user, keyed by hashed token.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return array
	 */
	private function get_sessions( $user_id ) {

		$sessions = get_user_meta( $user_id, 'session_tokens', true );

		if ( ! is_array( $sessions ) ) {
			return array();
		}

		$sessions = array_filter(
			$sessions,
			function ( $session ) {
				return is_array( $session ) && isset( $session['expiration'] ) && $session['expiration'] >= time();
			}
		);

		// Newest login first.
		uasort(
			$sessions,
			function ( $a, $b ) {
				return ( isset( $b['login'] ) ? $b['login'] : 0 ) - ( isset( $a['login'] ) ? $a['login'] : 0 );
			}
		);

		return $sessions;
	}

	/**
	 * Hash of the current request's session token, the same way core hashes it.
	 *
	 * @return string
	 */
	private function current_verifier() {
		$token = wp_get_session_token();

		return $token ? hash( 'sha256', $token ) : '';
	}

	/**
	 * Render the Active Sessions table on the profile screen.
	 *
	 * @param WP_User $user Profile user.
	 */
	public function render( $user ) {

		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}

		?>
		<h2 id="wpfl-sessions"><?php esc_html_e( 'Active Sessions', 'wp-force-logout' ); ?></h2>
		<?php

		if ( ! $this->is_supported() ) {
			echo '<p>' . esc_html__( 'Another plugin manages the login sessions on this site, so they can\'t be listed here.', 'wp-force-logout' ) . '</p>';
			return;
		}

		$sessions = $this->get_sessions( $user->ID );

		if ( empty( $sessions ) ) {
			echo '<p>' . esc_html__( 'No active sessions.', 'wp-force-logout' ) . '</p>';
			return;
		}

		$current     = get_current_user_id() === $user->ID ? $this->current_verifier() : '';
		$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		?>
		<table class="widefat striped" style="max-width: 900px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Device', 'wp-force-logout' ); ?></th>
					<th><?php esc_html_e( 'IP Address', 'wp-force-logout' ); ?></th>
					<th><?php esc_html_e( 'Logged In', 'wp-force-logout' ); ?></th>
					<th><?php esc_html_e( 'Expires', 'wp-force-logout' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $sessions as $verifier => $session ) : ?>
					<?php $ua = isset( $session['ua'] ) ? $session['ua'] : ''; ?>
					<tr>
						<td title="<?php echo esc_attr( $ua ); ?>">
							<?php echo esc_html( $this->describe_user_agent( $ua ) ); ?>
							<?php if ( hash_equals( (string) $verifier, $current ) ) : ?>
								<br/><strong><?php esc_html_e( 'This device', 'wp-force-logout' ); ?></strong>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( ! empty( $session['ip'] ) ? $session['ip'] : '—' ); ?></td>
						<td>
							<?php
							if ( ! empty( $session['login'] ) ) {
								/* translators: %s: human-readable time difference, e.g. 5 mins */
								echo esc_html( sprintf( __( '%s ago', 'wp-force-logout' ), human_time_diff( $session['login'] ) ) );
								echo '<br/><small>' . esc_html( wp_date( $date_format, $session['login'] ) ) . '</small>';
							} else {
								echo '—';
							}
							?>
						</td>
						<td><?php echo esc_html( wp_date( $date_format, $session['expiration'] ) ); ?></td>
						<td>
							<?php if ( ! hash_equals( (string) $verifier, $current ) ) : ?>
								<a class="button" href="<?php echo esc_url( $this->destroy_url( $user->ID, $verifier ) ); ?>"><?php esc_html_e( 'Log Out', 'wp-force-logout' ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * URL that logs out one session.
	 *
	 * @param int    $user_id  User ID.
	 * @param string $verifier Hashed session token.
	 *
	 * @return string
	 */
	private function destroy_url( $user_id, $verifier ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'action'  => 'wpfl_destroy_session',
					'user_id' => $user_id,
					'session' => $verifier,
				),
				admin_url( 'admin-post.php' )
			),
			'wpfl_destroy_session_' . $user_id . '_' . $verifier
		);
	}

	/**
	 * Log out one session.
	 */
	public function destroy_session() {

		$user_id  = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;
		$verifier = isset( $_GET['session'] ) ? sanitize_key( wp_unslash( $_GET['session'] ) ) : '';

		check_admin_referer( 'wpfl_destroy_session_' . $user_id . '_' . $verifier );

		if ( ! $user_id || ! current_user_can( 'edit_user', $user_id ) || ! $this->is_supported() ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'wp-force-logout' ), 403 );
		}

		$sessions = get_user_meta( $user_id, 'session_tokens', true );

		if ( is_array( $sessions ) && isset( $sessions[ $verifier ] ) ) {
			unset( $sessions[ $verifier ] );

			if ( $sessions ) {
				update_user_meta( $user_id, 'session_tokens', $sessions );
			} else {
				delete_user_meta( $user_id, 'session_tokens' );
			}
		}

		$redirect = get_current_user_id() === $user_id ? admin_url( 'profile.php' ) : add_query_arg( 'user_id', $user_id, admin_url( 'user-edit.php' ) );
		wp_safe_redirect( add_query_arg( 'wpfl_session_destroyed', 1, $redirect ) . '#wpfl-sessions' );
		exit;
	}

	/**
	 * Confirm the logout on the profile screen.
	 */
	public function notice() {

		if ( empty( $_GET['wpfl_session_destroyed'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only.
			return;
		}

		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'The session has been logged out.', 'wp-force-logout' ) . '</p></div>';
	}

	/**
	 * Short "Browser on OS" description of a user agent.
	 *
	 * @param string $ua User agent.
	 *
	 * @return string
	 */
	private function describe_user_agent( $ua ) {

		if ( '' === $ua ) {
			return __( 'Unknown device', 'wp-force-logout' );
		}

		// Order matters: Edge and Opera also claim to be Chrome, Chrome also claims to be Safari.
		$browsers = array(
			'Edg/'    => 'Edge',
			'OPR/'    => 'Opera',
			'Firefox' => 'Firefox',
			'Chrome'  => 'Chrome',
			'CriOS'   => 'Chrome',
			'Safari'  => 'Safari',
		);
		$systems  = array(
			'iPhone'   => 'iPhone',
			'iPad'     => 'iPad',
			'Android'  => 'Android',
			'Windows'  => 'Windows',
			'Mac OS X' => 'macOS',
			'CrOS'     => 'ChromeOS',
			'Linux'    => 'Linux',
		);

		$browser = __( 'Unknown browser', 'wp-force-logout' );
		foreach ( $browsers as $needle => $name ) {
			if ( false !== strpos( $ua, $needle ) ) {
				$browser = $name;
				break;
			}
		}

		foreach ( $systems as $needle => $name ) {
			if ( false !== strpos( $ua, $needle ) ) {
				/* translators: 1: browser name, 2: operating system */
				return sprintf( __( '%1$s on %2$s', 'wp-force-logout' ), $browser, $name );
			}
		}

		return $browser;
	}
}

new WPForce_Logout_Sessions();
