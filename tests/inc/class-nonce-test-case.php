<?php

class Nonce_Test_Case extends WP_UnitTestCase {
	protected static $default_action = -1;
	protected static $default_key    = '_wpnonce';
	protected static $url            = 'https://www.wordpress.org/plugins/?parameter=hey';

	// public function setUp() {
	// }

	/**
	 * Assertion to verify that a nonce is valid,
	 * useful because wp_verify_nonce() can return 1 or 2.
	 *
	 * @param string $nonce A generated nonce.
	 * @return bool Validity of the nonce
	 */
	public function assertValidNonce( $nonce ) {
		return $this->assertTrue( (bool) $nonce );
	}

	/**
	 * Assertion to verify that a nonce is invalid.
	 * A proxy function to assertFalse(), just to keep
	 * consistency with assertValidNonce() above
	 *
	 * @param string $nonce A generated nonce.
	 * @return bool Validity of the nonce
	 */
	public function assertInvalidNonce( $nonce ) {
		return $this->assertFalse( $nonce );
	}
}