<?php

namespace Pskli\Nonce;

/**
 * Default Nonce class.
 *
 * Allows user to create a basic WordPress nonce.
 */
class Nonce extends Nonce_Common {
	/**
	 * Generate the nonce.
	 *
	 * @return string $nonce The generated nonce string.
	 */
	public function create() {
		$nonce = wp_create_nonce( $this->get_action() );
		$this->set_nonce( $nonce );

		return $this->get_nonce();
	}

	/**
	 * Verify if a nonce is valid.
	 *
	 * @param string $value (optional) The value to verify against the generated nonce.
	 *                                 If no value is passed, the generated nonce will be used to check
	 *                                 if it is still valid, you know, just in case ¯\_(ツ)_/¯.
	 * @return false|int False if the nonce is invalid, 1 if the nonce is valid and generated between
	 *                   0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
	 */
	public function is_valid( $value = null ) {
		if ( is_null( $value ) ) {
			$value = $this->get_nonce();
		}

		return wp_verify_nonce( $value, $this->get_action() );
	}

	/**
	 * Verify if we are dealing with a valid admin request.
	 *
	 * @return false|int False if the nonce is invalid, 1 if the nonce is valid and generated between
	 *                   0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
	 */
	public function is_valid_admin_request() {
		return check_admin_referer( $this->get_action(), $this->get_key() );
	}

	/**
	 * Verify if we are dealing with a valid AJAX request.
	 *
	 * @param boolean $die
	 * @return false|int False if the nonce is invalid, 1 if the nonce is valid and generated between
	 *                   0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
	 */
	public function is_valid_ajax_request( $die = true ) {
		return check_ajax_referer( $this->get_action(), $this->get_key(), $die );
	}
}
