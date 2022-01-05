<?php

/**
 * Test class for all tests within WPForce Logout plugin.
 *
 * @since 1.5.0
 */
class WP_Force_Logout_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->class_instance = new WP_Force_Logout_Process();
	}

	/**
	 * Is user online tests
	 *
	 * @since 1.5.0
	 */
	public function test_is_user_online() {

		$result   = $this->class_instance->is_user_online( 'Invalid User ID passed.' );
		$expected = false;

		$this->assertEquals( $expected, $result );
	}
}
