<?php

namespace Inpsyde\Nonce;

class Nonce_Field extends Nonce_Common {
	protected $nonce_field;
	protected $referer;

	public function create( $referer = true ) {
		$this->setReferer( $referer );
		$nonce_field = wp_nonce_field( $this->getAction(), $this->getKey(), $this->getReferer(), false );
		$this->setNonceField( $nonce_field );
		$this->setNonce( wp_create_nonce( $this->getAction() ) );
		
		return $this->getNonceField();
	}

	public function setNonceField( $nonce_field ) {
		$this->nonce_field = $nonce_field;
		return $this->getNonceField();
	}

	public function getNonceField() {
		return $this->nonce_field;
	}
	
	public function setReferer( $referer ) {
		$this->referer = $referer;
		return $this->getReferer();
	}

	public function getReferer() {
		return $this->referer;
	}

	public function isValid( $value = null ) {
		if ( is_null( $value ) ) {
			$value = isset( $_REQUEST[ $this->getKey() ] ) ? $_REQUEST[ $this->getKey() ] : $this->getNonce();
		}

		return wp_verify_nonce( $value, $this->getAction() );
	}
}