<?php

namespace Inpsyde\Nonce;

/**
 * Nonce class for input fields.
 *
 * Allows user to generate/verify a nonce stored in a hidden input field.
 */
class Nonce_Field extends Nonce_Common {
	/**
	 * The generated hidden input field (HTML markup).
	 *
	 * @var string
	 */
	protected $nonce_field;

	/**
	 * The referer parameter.
	 *
	 * @var bool
	 */
	protected $referer;

	/**
	 * Generate the nonce hidden input field.
	 *
	 * @param bool $referer Whether to include a referer input field, or not.
	 * @return string $nonce_field The generated nonce field HTML markup string.
	 */
	public function create( $referer = true ) {
		$this->set_referer( $referer );
		$nonce_field = wp_nonce_field( $this->get_action(), $this->get_key(), $this->get_referer(), false );
		$this->set_nonce_field( $nonce_field );
		$this->set_nonce( wp_create_nonce( $this->get_action() ) );

		return $this->get_nonce_field();
	}

	/**
	 * Setter method to store the hidden input nonce field.
	 *
	 * @param string $nonce_field
	 * @return string $nonce_field The hidden input nonce field HTML markup.
	 */
	protected function set_nonce_field( string $nonce_field ) {
		$this->nonce_field = $nonce_field;
		return $this->get_nonce_field();
	}

	/**
	 * Getter method to get the hidden input nonce field.
	 *
	 * @return string $nonce_field The hidden input nonce field HTML markup.
	 */
	public function get_nonce_field() {
		return $this->nonce_field;
	}

	/**
	 * Setter method to store the referer parameter value.
	 *
	 * @param bool $referer
	 * @return bool $referer The referer parameter value.
	 */
	protected function set_referer( bool $referer ) {
		$this->referer = $referer;
		return $this->get_referer();
	}

	/**
	 * Setter method to store the referer parameter value.
	 *
	 * @return bool $referer The referer parameter value.
	 */
	public function get_referer() {
		return $this->referer;
	}

	/**
	 * Verify if a nonce is valid.
	 *
	 * @param string $value (optional) The value to verify against the generated nonce.
	 *                                 If no value is passed, the function will look for the request parameter
	 *                                 if it's present and check against it, otherwise the generated nonce
	 *                                 will be used to check if it is still valid, you know, just in case ¯\_(ツ)_/¯.
	 * @return false|int False if the nonce is invalid, 1 if the nonce is valid and generated between
	 *                   0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
	 */
	public function is_valid( $value = null ) {
		if ( is_null( $value ) ) {
			$value = isset( $_REQUEST[ $this->get_key() ] ) ? $_REQUEST[ $this->get_key() ] : $this->get_nonce();
		}

		return wp_verify_nonce( $value, $this->get_action() );
	}
}
