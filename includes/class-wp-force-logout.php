<?php
/**
 * Final Class File.
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
 * Main WP_Force_Logout Class.
 *
 * @class   WP_Force_Logout
 * @version 1.0.0
 */
final class WP_Force_Logout {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.5.0';


	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-force-logout' ), '1.0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-force-logout' ), '1.0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * WPForce Logout Constructor.
	 */
	public function __construct() {

		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		$this->define_constants();
		$this->includes();

		do_action( 'wp_force_logout_loaded' );
	}

	/**
	 * Define FT Constants.
	 */
	private function define_constants() {
		$this->define( 'WPFL_ABSPATH', dirname( WP_FORCE_LOGOUT_PLUGIN_FILE ) . '/' );
		$this->define( 'WPFL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'WPFL_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name Name of a constant.
	 * @param string|bool $value Value of a constant.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/wp-force-logout/wp-force-logout-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/wp-force-logout-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-force-logout' );

		load_textdomain( 'wp-force-logout', WP_LANG_DIR . '/wp-force-logout/wp-force-logout-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp-force-logout', false, plugin_basename( dirname( WP_FORCE_LOGOUT_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * Includes.
	 */
	private function includes() {
		include_once dirname( __FILE__ ) . '/class-wp-force-logout-process.php';

		if ( class_exists( 'WP_CLI' ) ) {
			include_once WPFL_ABSPATH . '/src/WPForce_Logout_CLI.php';
		}
	}
}
