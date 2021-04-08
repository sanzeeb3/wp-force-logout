<?php
/**
 * Uninstall WPForce Logout.
 *
 * @since 1.4.4
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete option set for the review notice.
delete_option( 'wpfl_review_notice_dismissed' );

// Delete online status stored in transient.
delete_transient( 'online_status' );

// Delete all users last_login meta key.
$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = 'last_login';" );
