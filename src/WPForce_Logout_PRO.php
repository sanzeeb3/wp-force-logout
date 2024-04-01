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
    private $settings = [];

   	/**
	 * Constructor.
	 */
	public function __construct() {

        $this->settings = get_option( 'wp_force_logout_settings' );

        add_filter( 'logout_url', [ $this, 'logout_url' ], PHP_INT_MAX, 10, 2 );
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
}

new WPForce_Logout_PRO();