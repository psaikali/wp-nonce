<?php

namespace Inpsyde\Nonce;

abstract class Nonce_Common {
	private static $default_action = -1;
	private static $default_key    = '_wpnonce';
	protected $action;
	protected $key;
	protected $nonce;

	public function __construct( $action = null, $key = null ) {
		$this->setAction( $action );
		$this->setKey( $key );
	}

	public function __toString() {
		return $this->nonce;
	}

	public function setAction( $action ) {
		if ( is_null( $action ) ) {
			$this->action = $this->getDefaultAction();
		} else {
			$this->action = $action;
		}

		return $this->getAction();
	}

	public function setKey( $key ) {
		if ( is_null( $key ) ) {
			$this->action = $this->getDefaultKey();
		} else {
			$this->key = $key;
		}
		
		return $this->getKey();
	}

	public function setNonce( $nonce ) {
		$this->nonce = $nonce;
		return $this->getNonce();
	}

	public function getAction() {
		return ( is_null( $this->action ) ) ? $this->getDefaultAction() : $this->formatAction( $this->action );
	}

	public function getDefaultAction() {
		return apply_filters( 'inpsyde.nonce.default_action', self::$default_action );
	}

	public function getKey() {
		return $this->key;
	}

	public function getDefaultKey() {
		$this->key = apply_filters( 'inpsyde.nonce.default_key', self::$default_key );
	}

	public function getNonce() {
		return $this->nonce;
	}

	/**
	 * Format an action by giving a simple string, 
	 * or an array like [ 'action_%s_%d', [ 'string', 123 ] ] to generate a dynamic action string using vsprintf()
	 *
	 * @param string|array $action The action to format: a simple string, or an array to use just like vsprintf() to render a dynamic string.
	 * @return string|WP_Error Formatted action string, or a WP_Error if the $action parameter is an invalid array to use with vsprintf().
	 */
	protected function formatAction( $action ) {
		if ( is_scalar( $action ) ) {
			return $action;
		}

		if ( is_array( $action ) ) {
			if ( ! isset( $action[0] ) || ! isset( $action[1] ) || ! is_string( $action[0] ) || ! is_array( $action[1] ) ) {
				return new \WP_Error( 'invalid_arguments', 'When passing an array as an action, please provide a string as first item and an array as second item.', $action );
			}

			$format = $action[0];
			$args   = $action[1];

			return vsprintf( $format, $args );
		}
	}
}