<?php
/**
 * WP Force Logout PRO Dashboard Widget File.
 *
 * @package    WP Force Logout
 * @author     Sanjeev Aryal
 * @since      3.0.0
 * @license    GPL-3.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}

/**
 * "Who's Online" dashboard widget with a logout link per user.
 *
 * @since 3.0.0
 */
class WPForce_Logout_Dashboard {

	/**
	 * Most users listed in the widget.
	 */
	const LIMIT = 20;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_widget' ) );
	}

	/**
	 * Register the widget for users who can force logout.
	 */
	public function add_widget() {

		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		wp_add_dashboard_widget( 'wp_force_logout_online_users', __( 'Who\'s Online', 'wp-force-logout' ), array( $this, 'render' ) );
	}

	/**
	 * Render the widget.
	 */
	public function render() {

		$online = array();
		foreach ( (array) get_transient( 'online_status' ) as $user_id => $last_seen ) {
			// Same 60 seconds window as the Login Activity column.
			if ( $user_id && $last_seen > time() - MINUTE_IN_SECONDS ) {
				$online[ (int) $user_id ] = (int) $last_seen;
			}
		}

		arsort( $online );

		if ( empty( $online ) ) {
			echo '<p>' . esc_html__( 'No one is online right now.', 'wp-force-logout' ) . '</p>';
			return;
		}

		$users = get_users(
			array(
				'include' => array_slice( array_keys( $online ), 0, self::LIMIT ),
				'orderby' => 'include',
			)
		);

		$role_names = wp_roles()->get_names();
		?>
		<p>
			<?php
			/* translators: %d: number of online users */
			echo esc_html( sprintf( _n( '%d user online', '%d users online', count( $online ), 'wp-force-logout' ), count( $online ) ) );
			?>
		</p>
		<ul class="wpfl-online-users" style="margin:0;">
			<?php foreach ( $users as $user ) : ?>
				<?php
				$roles = array();
				foreach ( $user->roles as $role ) {
					$roles[] = isset( $role_names[ $role ] ) ? translate_user_role( $role_names[ $role ] ) : $role;
				}
				?>
				<li style="display:flex; align-items:center; gap:8px; padding:6px 0; border-bottom:1px solid #f0f0f1;">
					<?php echo get_avatar( $user->ID, 32 ); ?>
					<span style="flex:1;">
						<a href="<?php echo esc_url( get_edit_user_link( $user->ID ) ); ?>"><strong><?php echo esc_html( $user->display_name ); ?></strong></a>
						<?php if ( get_current_user_id() === $user->ID ) : ?>
							(<?php esc_html_e( 'you', 'wp-force-logout' ); ?>)
						<?php endif; ?>
						<br/><small><?php echo esc_html( implode( ', ', $roles ) ); ?></small>
					</span>
					<?php if ( get_current_user_id() !== $user->ID ) : ?>
						<a style="color:#b32d2e;" href="<?php echo esc_url( $this->logout_url( $user->ID ) ); ?>"><?php echo esc_html_x( 'Logout', 'The action on users list page', 'wp-force-logout' ); ?></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<p style="margin-bottom:0;">
			<a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>"><?php esc_html_e( 'View login activity of all users', 'wp-force-logout' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Logout link, handled by WP_Force_Logout_Process::trigger_query_actions() on the users page.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return string
	 */
	private function logout_url( $user_id ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'wpfl-logout',
					'user'   => $user_id,
				),
				admin_url( 'users.php' )
			),
			'wpfl-logout'
		);
	}
}

new WPForce_Logout_Dashboard();
