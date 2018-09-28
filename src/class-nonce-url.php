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
	public function create( $url ) {
		$this->setUrl( $url );
		$nonce_url = wp_nonce_url( $this->getUrl(), $this->getAction(), $this->getKey() );
		$this->setNonceUrl( $nonce_url );
		$this->setNonce( wp_create_nonce( $this->getAction() ) );
		
		return $this->getNonceUrl();
	}

	/**
	 * Setter method to store the original URL.
	 *
	 * @param string $url
	 * @return string $url The original URL to be nonced.
	 */
	protected function setUrl( $url ) {
		$this->url = $url;
		return $this->getUrl();
	}

	/**
	 * Getter method to get the original URL.
	 *
	 * @return string $url The original URL to be nonced.
	 */
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 * Setter method to store the nonced URL.
	 *
	 * @param string $nonce_url
	 * @return string $nonce_url The nonced URL.
	 */
	protected function setNonceUrl( $nonce_url ) {
		$this->nonce_url = $nonce_url;
		return $this->getNonceUrl();
	}

	/**
	 * Getter method to get the nonced URL.
	 *
	 * @return string $nonce_url The nonced URL.
	 */
	public function getNonceUrl() {
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
	public function isValid( $value = null ) {
		if ( is_null( $value ) ) {
			$value = isset( $_GET[ $this->getKey() ] ) ? $_GET[ $this->getKey() ] : $this->getNonce();
		}

		return wp_verify_nonce( $value, $this->getAction() );
	}
}