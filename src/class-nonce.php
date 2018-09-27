<?php

namespace Inpsyde\Nonce;

class Nonce extends Nonce_Common {
	public function create() {
		$nonce = wp_create_nonce( $this->getAction() );
		$this->setNonce( $nonce );
		return $nonce;
	}
	
	public function isValid( $value = null ) {
		if ( is_null( $value ) ) {
			$value = $this->getNonce();
		}

		return wp_verify_nonce( $value, $this->getAction() );
	}

	public function isValidAdminRequest() {

	}

	public function isValidAjaxRequest() {

	}
}