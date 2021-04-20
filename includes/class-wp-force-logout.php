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
	exit; // Exit if accessed directly.
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
	public $version = '1.4.5';


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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-force-logout' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-force-logout' ), '1.0' );
	}

	/**
	 * WPForms Entries Constructor.
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
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 *
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
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
	}
}
