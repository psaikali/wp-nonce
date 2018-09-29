<?php

namespace Inpsyde\Nonce;

/**
 * Nonce class for URLs.
 *
 * Allows user to insert/verify a nonce stored in a URL parameter.
 */
class Nonce_URL extends Nonce_Common {
	/**
	 * The original URL that should be nonce.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * The generated URL, which is the original URL with a nonce parameter in it.
	 *
	 * @var string
	 */
	protected $nonce_url;

	/**
	 * Generate the nonce URL.
	 *
	 * @param string $url The original URL.
	 * @return string $nonce_url The generated nonce URL string.
	 */
	public function create( string $url ) {
		$this->set_url( $url );
		$nonce_url = wp_nonce_url( $this->get_url(), $this->get_action(), $this->get_key() );
		$this->set_nonce_url( $nonce_url );
		$this->set_nonce( wp_create_nonce( $this->get_action() ) );

		return $this->get_nonce_url();
	}

	/**
	 * Setter method to store the original URL.
	 *
	 * @param string $url
	 * @return string $url The original URL to be nonced.
	 */
	protected function set_url( string $url ) {
		$this->url = $url;
		return $this->get_url();
	}

	/**
	 * Getter method to get the original URL.
	 *
	 * @return string $url The original URL to be nonced.
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Setter method to store the nonced URL.
	 *
	 * @param string $nonce_url
	 * @return string $nonce_url The nonced URL.
	 */
	protected function set_nonce_url( string $nonce_url ) {
		$this->nonce_url = $nonce_url;
		return $this->get_nonce_url();
	}

	/**
	 * Getter method to get the nonced URL.
	 *
	 * @return string $nonce_url The nonced URL.
	 */
	public function get_nonce_url() {
		return $this->nonce_url;
	}

	/**
	 * Verify if a nonce is valid.
	 *
	 * @param string $value (optional) The value to verify against the generated nonce.
	 *                                 If no value is passed, the function will look for the URL parameter itself
	 *                                 if it's present and check against it, otherwise the generated nonce
	 *                                 will be used to check if it is still valid, you know, just in case ¯\_(ツ)_/¯.
	 * @return false|int False if the nonce is invalid, 1 if the nonce is valid and generated between
	 *                   0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
	 */
	public function is_valid( $value = null ) {
		if ( is_null( $value ) ) {
			$value = isset( $_GET[ $this->get_key() ] ) ? $_GET[ $this->get_key() ] : $this->get_nonce();
		}

		return wp_verify_nonce( $value, $this->get_action() );
	}
}
