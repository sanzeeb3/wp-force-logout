<?php
/**
 * WP Force Logout PRO Settings File.
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

/**
 * WP Force Logout PRO Settings Class.
 *
 * @since 2.0.0
 */
class WPForce_Logout_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_wp_force_logout_submenu' ) );
	}

	/**
	 * Add WP Force Logout submenu.
	 *
	 * @since 2.0.0
	 */
	public function add_wp_force_logout_submenu() {
		$hook = add_users_page(
			'WPForce Logout', // page title
			'WPForce Logout', // menu title
			'manage_options', // capability
			'wp-force-logout', // menu slug
			array( $this, 'render_page' ) // callback function
		);

		add_action( 'load-' . $hook, array( $this, 'save_settings' ) );
		add_action( 'load-' . $hook, array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue the settings page script.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'wp-force-logout-settings', plugins_url( 'assets/js/pro/settings.js', WP_FORCE_LOGOUT_PLUGIN_FILE ), array(), WPFL_VERSION, true );
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
			array(
				'idle_logout'           => array(
					'id'      => 'wp-force-logout-idle-logout',
					'name'    => 'wpfl_idle_logout',
					'type'    => 'checkbox',
					'default' => 'off',
					'label'   => __( 'Idle user logout', 'wp-force-logout' ),
					'desc'    => __( 'Check to enable logging out idle users after specific time period.', 'wp-force-logout' ),
				),
				'idle_logout_period'    => array(
					'id'      => 'wp-force-logout-idle-logout-period',
					'name'    => 'wpfl_idle_logout_period',
					'type'    => 'number',
					'default' => '30',
					'label'   => __( 'Idle time to logout (in minutes)', 'wp-force-logout' ),
					'show_if' => 'wp-force-logout-idle-logout',
				),
				'idle_logout_roles'     => array(
					'id'      => 'wp-force-logout-idle-logout-roles',
					'name'    => 'wpfl_idle_logout_roles',
					'type'    => 'roles',
					'default' => array(),
					'label'   => __( 'Roles to logout when idle', 'wp-force-logout' ),
					'desc'    => __( 'Only users with the checked roles are logged out when idle. Leave all unchecked to apply it to every user.', 'wp-force-logout' ),
					'show_if' => 'wp-force-logout-idle-logout',
				),
				'browser_close_logout'  => array(
					'id'      => 'wp-force-logout-browser-close-logout',
					'name'    => 'wpfl_browser_close_logout',
					'type'    => 'checkbox',
					'default' => 'off',
					'label'   => __( 'Auto logout on browser close', 'wp-force-logout' ),
					/* translators: %d: minutes */
					'desc'    => sprintf( __( 'Log users out when they close the browser, "Remember Me" included. Browsers that reopen the previous session log them out once no page of the site was open for %d minutes.', 'wp-force-logout' ), WPForce_Logout_PRO::browser_close_timeout() / MINUTE_IN_SECONDS ),
				),
				'session_expiration'    => array(
					'id'      => 'wp-force-logout-session-expiration',
					'name'    => 'wpfl_session_expiration',
					'type'    => 'number',
					'default' => '',
					'label'   => __( 'Session Expiration (in minutes)', 'wp-force-logout' ),
					'desc'    => __( 'Set a maximum session duration after which users are automatically logged out. WP Default is 48 hours. 14 days if Remember Me is checked.', 'wp-force-logout' ),
				),
				'single_session'        => array(
					'id'      => 'wp-force-logout-single-session',
					'name'    => 'wpfl_single_session',
					'type'    => 'checkbox',
					'default' => 'off',
					'label'   => __( 'Single Session', 'wp-force-logout' ),
					'desc'    => __( 'Allow only one active login per user. New login from another device invalidates old session.', 'wp-force-logout' ),
				),
				'password_change'       => array(
					'id'      => 'wp-force-logout-password-change',
					'name'    => 'wpfl_password_change',
					'type'    => 'checkbox',
					'default' => 'off',
					'label'   => __( 'Password Change', 'wp-force-logout' ),
					'desc'    => __( 'Immediately logout all sessions when password changes.', 'wp-force-logout' ),
				),
				'scheduled_logout'      => array(
					'id'      => 'wp-force-logout-scheduled-logout',
					'name'    => 'wpfl_scheduled_logout',
					'type'    => 'checkbox',
					'default' => 'off',
					'label'   => __( 'Scheduled Logout', 'wp-force-logout' ),
					'desc'    => __( 'Logout all users every day at a specific time.', 'wp-force-logout' ),
				),
				'scheduled_logout_time' => array(
					'id'      => 'wp-force-logout-scheduled-logout-time',
					'name'    => 'wpfl_scheduled_logout_time',
					'type'    => 'time',
					'default' => '00:00',
					'label'   => __( 'Logout all users at', 'wp-force-logout' ),
					/* translators: %s: site timezone, e.g. Asia/Kathmandu or UTC+5:45 */
					'desc'    => sprintf( __( 'Site time (%s). Runs on the first visit to the site after this time, like all WordPress scheduled tasks.', 'wp-force-logout' ), wp_timezone_string() ),
					'show_if' => 'wp-force-logout-scheduled-logout',
				),
				'logout_redirect'       => array(
					'id'      => 'wp-force-logout-logout-redirect',
					'name'    => 'wpfl_logout_redirect',
					'type'    => 'url',
					'default' => '',
					'label'   => __( 'Logout Redirect', 'wp-force-logout' ),
					'desc'    => __( 'Leave empty for default. Default is usually login page URL unless other plugins overwrite.', 'wp-force-logout' ),
				),
			)
		);
	}

	/**
	 * Render Page.
	 *
	 * @since 2.0.0
	 */
	public function render_page() {

		$saved = get_option( 'wp_force_logout_settings', array() );

		?>
		<?php do_action( 'wp_force_logout_settings_init' ); ?>
		<h1>WPForce Logout PRO</h1><hr/>
		<div class="wp-force-logout-clicks-settings-container">
			<div class="wp-force-logout-clicks-settings-settings" style="max-width: 80%">
				<form method="post">
					<table class="form-table">
						<?php foreach ( (array) $this->get_settings() as $key => $settings ) : ?>
						<tr valign="top" class="<?php echo esc_attr( $settings['id'] ); ?>"<?php echo ! empty( $settings['show_if'] ) ? ' data-show-if="' . esc_attr( $settings['show_if'] ) . '"' : ''; ?>>
							<th scope="row"><label for="<?php echo esc_attr( $settings['id'] ); ?>"><?php echo esc_html( $settings['label'] ); ?></label></th>
								<td>
									<?php
									$value = isset( $saved[ $key ] ) ? $saved[ $key ] : $settings['default'];
									switch ( $settings['type'] ) {
										case 'checkbox':
											echo '<fieldset>';
											?>
											<label for="<?php echo esc_attr( $settings['id'] ); ?>" >
												<input type="checkbox"
													id="<?php echo esc_attr( $settings['id'] ); ?>"
													name="<?php echo esc_attr( $settings['name'] ); ?>"
													<?php checked( $value, 'on', true ); ?>
												/>
												<?php echo esc_html( $settings['desc'] ); ?>
											</label>
											<?php
											echo '</fieldset>';
											break;

										case 'roles':
											echo '<fieldset id="' . esc_attr( $settings['id'] ) . '">';
											foreach ( wp_roles()->get_names() as $role => $role_name ) {
												?>
												<label style="display:block; margin-bottom:4px;">
													<input type="checkbox"
														name="<?php echo esc_attr( $settings['name'] ); ?>[]"
														value="<?php echo esc_attr( $role ); ?>"
														<?php checked( in_array( $role, (array) $value, true ) ); ?>
													/>
													<?php echo esc_html( translate_user_role( $role_name ) ); ?>
												</label>
												<?php
											}
											echo '<p><i>' . esc_html( $settings['desc'] ) . '</i></p>';
											echo '</fieldset>';
											break;

										default:
											?>
												<input type="<?php echo esc_attr( $settings['type'] ); ?>"
													value="<?php echo esc_attr( $value ); ?>"
													id="<?php echo esc_attr( $settings['id'] ); ?>"
													name="<?php echo esc_attr( $settings['name'] ); ?>"
													<?php echo 'number' === $settings['type'] ? 'min="1"' : ''; ?>
												/>
												<?php
												if ( ! empty( $settings['desc'] ) ) {
													echo '<p><i>' . esc_html( $settings['desc'] ) . '</i></p>';
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

		$save_to_db = array();
		foreach ( $this->get_settings() as $key => $settings ) {
			$raw                = isset( $_POST[ $settings['name'] ] ) ? wp_unslash( $_POST[ $settings['name'] ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized by type below.
			$save_to_db[ $key ] = $this->sanitize( $settings['type'], $raw );
		}

		$option = get_option( 'wp_force_logout_settings', array() );

		$save_to_db = array_merge( (array) $option, $save_to_db );

		update_option( 'wp_force_logout_settings', $save_to_db );

		/**
		 * Fires after the PRO settings are saved.
		 *
		 * @since 3.0.0
		 *
		 * @param array $save_to_db Saved settings.
		 */
		do_action( 'wp_force_logout_settings_saved', $save_to_db );

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

	/**
	 * Sanitize a submitted value by its field type.
	 *
	 * @since 3.0.0
	 *
	 * @param string       $type Field type.
	 * @param string|array $raw  Submitted value.
	 *
	 * @return string|array
	 */
	private function sanitize( $type, $raw ) {

		switch ( $type ) {
			case 'checkbox':
				return empty( $raw ) ? '' : 'on';

			case 'number':
				return is_numeric( $raw ) && $raw > 0 ? (string) absint( $raw ) : '';

			case 'url':
				return is_string( $raw ) ? esc_url_raw( $raw ) : '';

			case 'time':
				return is_string( $raw ) && preg_match( '/^([01]\d|2[0-3]):[0-5]\d$/', $raw ) ? $raw : '';

			case 'roles':
				return array_values( array_intersect( array_map( 'sanitize_key', (array) $raw ), array_keys( wp_roles()->get_names() ) ) );

			default:
				return is_string( $raw ) ? sanitize_text_field( $raw ) : '';
		}
	}
}

new WPForce_Logout_Settings();
