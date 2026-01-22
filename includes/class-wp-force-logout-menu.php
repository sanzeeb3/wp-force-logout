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
	}

	/**
	 * Add WP Force Logout submenu.
	 *
	 * @since 2.0.0
	 */
	public function add_wp_force_logout_submenu() {
		add_users_page(
			'WPForce Logout', // page title
			'WPForce Logout', // menu title
			'manage_options', // capability
			'wp-force-logout', // menu slug
			[ $this, 'render_page' ] // callback function
		);
	}

	/**
	 * Get the Settings.
	 *
	 * @since 2.0.0
	 *
	 * @return array.
	 */
	public function get_settings() {

		return apply_filters(
			'wp_force_logout_settings',
			[
				'idle_logout'        => [
					'id'      => 'wp-force-logout-idle-logout',
					'name'    => 'wpfl_idle_logout',
					'type'    => 'checkbox',
					'default' => 'off',
					'label'   => __( 'Idle user logout', 'wp-force-logout' ),
					'desc'    => __( 'Check to enable logging out idle users after specific time period.', 'wp-force-logout' ),
				],
				'idle_logout_period'        => [
					'id'      => 'wp-force-logout-idle-logout-period',
					'name'    => 'wpfl_idle_logout_period',
					'type'    => 'number',
					'default' => '30',
					'label'   => __( 'Idle time to logout (in minutes)', 'wp-force-logout' ),
				],
				'browser_close_logout'        => [
					'id'      => 'wp-force-logout-browser-close-logout',
					'name'    => 'wpfl_browser_close_logout',
					'type'    => 'checkbox',
					'default' => 'off',
					'label'   => __( 'Auto logout on browser close', 'wp-force-logout' ),
					'desc'    => __( 'Check to enable logging out user when they close the browser.', 'wp-force-logout' ),
				],
				'session_expiration'        => [
					'id'      => 'wp-force-logout-session-expiration',
					'name'    => 'wpfl_session_expiration',
					'type'    => 'number',
					'default' => '',
					'label'   => __( 'Session Expiration (in minutes)', 'wp-force-logout' ),
					'desc'    => __( 'Set a maximum session duration after which users are automatically logged out. Default 48 hours. 14 days if Remember Me is checked.', 'wp-force-logout' ),
				],
				'single_session'        => [
					'id'      => 'wp-force-logout-single-session',
					'name'    => 'wpfl_single_session',
					'type'    => 'checkbox',
					'default' => 'off',
					'label'   => __( 'Single Session', 'wp-force-logout' ),
					'desc'    => __( 'Allow only one active login per user. New login from another device invalidates old session.', 'wp-force-logout' ),
				],
				'password_change'        => [
					'id'      => 'wp-force-logout-password-change',
					'name'    => 'wpfl_password_change',
					'type'    => 'checkbox',
					'default' => 'off',
					'label'   => __( 'Password Change', 'wp-force-logout' ),
					'desc'    => __( 'Immediately logout all sessions when password changes.', 'wp-force-logout' ),
				],
				'logout_redirect'        => [
					'id'      => 'wp-force-logout-logout-redirect',
					'name'    => 'wpfl_logout_redirect',
					'type'    => 'url',
					'default' => '',
					'label'   => __( 'Logout Redirect', 'wp-force-logout' ),
					'desc'    => __( 'Leave empty for default. Default is usually login page URL unless other plugins overwrite.', 'wp-force-logout' ),
				],
			]
		);
	}

	/**
	 * Render Page.
	 *
	 * @since 2.0.0
	 */
	public function render_page() {

		$this->save_settings();

		?>	
		<?php do_action( 'wp_force_logout_settings_init' ); ?>
		<h1>WPForce Logout PRO</h1><hr/>
		<div class="wp-force-logout-clicks-settings-container">
			<div class="wp-force-logout-clicks-settings-settings" style="max-width: 80%">
				<form method="post">
					<table class="form-table">
						<?php foreach ( (array) $this->get_settings() as $key => $settings ) : ?>
						<tr valign="top" class="<?php echo esc_attr( $settings['id'] ); ?>">
							<th scope="row"><label for="<?php echo esc_attr( $settings['id'] ); ?>"><?php echo esc_html( $settings['label'] ); ?></label></th>
								<td>
									<?php
									$saved = get_option( 'wp_force_logout_settings' );
									switch ( $settings['type'] ) {
										case 'checkbox':
											$value = isset( $saved[ $key ] ) ? $saved[ $key ] : $settings['default'];
											echo '<fieldset>';
											?>
											<label for="<?php echo esc_attr( $settings['id'] ); ?>" >
												<input type="checkbox"
													id="<?php echo esc_attr( $settings['id'] ); ?>"
													name="<?php echo esc_attr( $settings['name'] ); ?>"
													<?php checked( $value, 'on', true ); ?>
												/>
												<?php esc_html_e( $settings['desc'] ); ?>
											</label>
											<?php
											echo '</fieldset>';
											break;

										default:
											?>
												<input type="<?php echo esc_attr( $settings['type'] ); ?>"
													value="<?php echo isset( $saved[ $key ] ) ? esc_attr( $saved[ $key ] ) : esc_attr( $settings['default'] ); ?>"
													id="<?php echo esc_attr( $settings['id'] ); ?>"
													name="<?php echo esc_attr( $settings['name'] ); ?>"
												/>
												<?php
													if ( ! empty( $settings['desc'] ) ) {
														echo '<p><i>'. esc_html( $settings['desc'] ) . '<i></p>'; 
													}
												?>
											<?php
									}//end switch
									?>
								</td>
						</tr>
						<?php endforeach; ?>

					</table>
						<?php wp_nonce_field( 'wp_force_logout_settings', 'wp_force_logout_settings_nonce' ); ?>
						<?php submit_button(); ?>
				</form>
			</div>

			<?php do_action( 'wp_force_logout_settings_after' ); ?>
		</div>
		<?php
	}

	/**
	 * Save settings to the database.
	 *
	 * @since 2.0.0
	 *
	 * @return void.
	 */
	public function save_settings() {

		if ( ! isset( $_POST['submit'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if (
			! isset( $_POST['wp_force_logout_settings_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['wp_force_logout_settings_nonce'] ), 'wp_force_logout_settings' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		) {
			return;
		}

		$save_to_db = [];
		foreach ( $this->get_settings() as $key => $settings ) {
			$save_to_db[ $key ] = ! empty( $_POST[ $settings['name'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $settings['name'] ] ) ) : '';
		}

		$option = get_option( 'wp_force_logout_settings', [] );

		$save_to_db = array_merge( $option, $save_to_db );

		update_option( 'wp_force_logout_settings', $save_to_db );

		add_action(
			'wp_force_logout_settings_init',
			static function () {
				?>
				<div style="margin-left: 0px;" class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e( 'Settings Saved.', 'wp-force-logout' ); ?></strong></p>
				</div>
				<?php
			}
		);
	}
}

new WP_Force_Logout_Menu();
