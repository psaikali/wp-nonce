<?php

namespace Inpsyde\Nonce;

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
		$nonce = wp_create_nonce( $this->getAction() );
		$this->setNonce( $nonce );

		return $this->getNonce();
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
	public function isValid( $value = null ) {
		if ( is_null( $value ) ) {
			$value = $this->getNonce();
		}

		return wp_verify_nonce( $value, $this->getAction() );
	}

	/**
	 * Verify if we are dealing with a valid admin request.
	 *
	 * @return false|int False if the nonce is invalid, 1 if the nonce is valid and generated between
	 *                   0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
	 */
	public function isValidAdminRequest() {
		return check_admin_referer( $this->getAction(), $this->getKey() );
	}

	/**
	 * Verify if we are dealing with a valid AJAX request.
	 *
	 * @param boolean $die
	 * @return false|int False if the nonce is invalid, 1 if the nonce is valid and generated between
	 *                   0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
	 */
	public function isValidAjaxRequest( $die = true ) {
		return check_ajax_referer( $this->getAction(), $this->getKey(), $die );
	}
}