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
	 * @param bool $referer Whether to include a referer input field, or not
	 * @return string $nonce_field The generated nonce field HTML markup string.
	 */
	public function create( $referer = true ) {
		$this->setReferer( $referer );
		$nonce_field = wp_nonce_field( $this->getAction(), $this->getKey(), $this->getReferer(), false );
		$this->setNonceField( $nonce_field );
		$this->setNonce( wp_create_nonce( $this->getAction() ) );
		
		return $this->getNonceField();
	}

	/**
	 * Setter method to store the hidden input nonce field.
	 *
	 * @param string $nonce_field
	 * @return string $nonce_field The hidden input nonce field HTML markup.
	 */
	public function setNonceField( $nonce_field ) {
		$this->nonce_field = $nonce_field;
		return $this->getNonceField();
	}

	/**
	 * Getter method to get the hidden input nonce field.
	 *
	 * @return string $nonce_field The hidden input nonce field HTML markup.
	 */
	public function getNonceField() {
		return $this->nonce_field;
	}
	
	/**
	 * Setter method to store the referer parameter value.
	 *
	 * @param bool $referer
	 * @return bool $referer The referer parameter value.
	 */
	public function setReferer( $referer ) {
		$this->referer = $referer;
		return $this->getReferer();
	}

	/**
	 * Setter method to store the referer parameter value.
	 *
	 * @return bool $referer The referer parameter value.
	 */
	public function getReferer() {
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
	public function isValid( $value = null ) {
		if ( is_null( $value ) ) {
			$value = isset( $_REQUEST[ $this->getKey() ] ) ? $_REQUEST[ $this->getKey() ] : $this->getNonce();
		}

		return wp_verify_nonce( $value, $this->getAction() );
	}
}