<?php

namespace Inpsyde\Nonce;

class Nonce_URL extends Nonce_Common {
	protected $url;
	protected $nonce_url;

	public function create( $url ) {
		$this->setUrl( $url );
		$nonce_url = wp_nonce_url( $this->getUrl(), $this->getAction(), $this->getKey() );
		$this->setNonceUrl( $nonce_url );
		$this->setNonce( wp_create_nonce( $this->getAction() ) );
		
		return $this->getNonceUrl();
	}

	public function setUrl( $url ) {
		$this->url = $url;
		return $this->getUrl();
	}

	public function getUrl() {
		return $this->url;
	}
	
	public function setNonceUrl( $nonce_url ) {
		$this->nonce_url = $nonce_url;
		return $this->getNonceUrl();
	}

	public function getNonceUrl() {
		return $this->nonce_url;
	}

	public function isValid( $value = null ) {
		if ( is_null( $value ) ) {
			$value = isset( $_GET[ $this->getKey() ] ) ? $_GET[ $this->getKey() ] : $this->getNonce();
		}

		return wp_verify_nonce( $value, $this->getAction() );
	}
}