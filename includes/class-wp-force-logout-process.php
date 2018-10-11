<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * @class WP_Force_Logout_Process
 * @since  1.0.0 
 */
Class WP_Force_Logout_Process {

	/**
	 * Constructor.
	 */
	public function __construct() {
		wp_enqueue_style( 'wp-force-logout', plugins_url( '/entries-for-wpforms/assets/css/wp-force-logout.css' ), array(), WPFL_VERSION, $media = 'all' );
		add_filter( 'manage_users_columns', array( $this, 'add_column_title' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'add_column_value' ), 10, 3 );
	}

	/**
	 * Add the column title for the login activity column
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_column_title( $columns ) {

		if ( ! current_user_can( 'edit_user' ) ) {
			return $columns;
		}

		$new_columns['wpfl'] = __( 'Login Activity', 'wp-force-logout' );
		
		return $this->custom_insert_after_helper( $columns, $new_columns, 'cb' );
	}

	/**
	 * Insert Login Activity after first checkbox column
	 * @param  array  $columns     WP_User_List_Table columns
	 * @param  array  $new_column  New Columns to insert
	 * @param  string $after       Position of new column after
	 * @return array  Columns.
	 */
	public function custom_insert_after_helper( $columns, $new_columns, $after ) {

		// Search for the item position and +1 since is after the selected item key.
		$position = array_search( $after, array_keys( $columns ) ) + 1;

		// Insert the new item.
		$return_columns = array_slice( $columns, 0, $position, true );
		$return_columns += $new_columns;
		$return_columns += array_slice( $columns, $position, count( $columns ) - $position, true );

	    return $return_columns;
	}

	/**
	 * Set the value for login activity column for each user in the users list
	 *
	 * @param string $value 	  Value to display
	 * @param string $column_name Column Name.
	 * @param int $user_id		  User ID.
	 *
	 * @return string
	 */
	public function add_column_cell( $value, $column_name, $user_id ) {

		if ( ! current_user_can( 'edit_user' ) ) {
			return false;
		}

		if ( $column_name == 'wpfl') {
			$value = 'logged in!';
		}

		return $value;
	}
}

new WP_Force_Logout_Process();
