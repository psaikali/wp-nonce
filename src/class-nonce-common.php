<?php

namespace Inpsyde\Nonce;

/**
 * Common Nonce abstract class.
 * 
 * Shared across all subclasses (Nonce, Nonce_URL, Nonce_Field)
 * to access common methods. Do not instantiate.
 */
abstract class Nonce_Common {
	/**
	 * Default action to be used if no nonce action is set by user
	 *
	 * @var integer
	 */
	private static $default_action = -1;

	/**
	 * Default key to be used if no key is set by user
	 *
	 * @var string
	 */
	private static $default_key = '_wpnonce';

	/**
	 * The original action passed by user on instantiation
	 *
	 * @var string|array
	 */
	protected $action;

	/**
	 * The key used as URL or input parameter name
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * The generated nonce
	 *
	 * @var string
	 */
	protected $nonce;

	/**
	 * Common constructor to instantiate a new nonce.
	 * Does not create the nonce itself yet, but stores the action and key.
	 *
	 * @param string|array $action
	 * @param string $key
	 */
	public function __construct( $action = null, $key = null ) {
		$this->setAction( $action );
		$this->setKey( $key );
	}

	/**
	 * Setter method to set the $action.
	 *
	 * @param string|float|int|array $action
	 * @return string $action The formatted action
	 */
	protected function setAction( $action ) {
		if ( is_scalar( $action ) || is_array( $action ) ) {
			$this->action = $action;
		} else {
			$this->action = $this->getDefaultAction();
		}

		return $this->getAction();
	}

	/**
	 * Setter method to set the $key.
	 *
	 * @param string $key
	 * @return string $key The key
	 */
	protected function setKey( $key ) {
		if ( is_null( $key ) ) {
			$this->key = $this->getDefaultKey();
		} else {
			$this->key = $key;
		}
		
		return $this->getKey();
	}

	/**
	 * Setter method to set the $nonce.
	 *
	 * @param string $nonce
	 * @return string $nonce
	 */
	protected function setNonce( $nonce ) {
		$this->nonce = $nonce;
		return $this->getNonce();
	}

	/**
	 * Getter method to get the action and format it.
	 *
	 * @return string $action
	 */
	public function getAction() {
		return ( is_null( $this->action ) ) ? $this->getDefaultAction() : $this->formatAction( $this->action );
	}

	/**
	 * Getter method to get the default action.
	 *
	 * @return string
	 */
	public function getDefaultAction() {
		/**
		 * This filter lets user change, via code, the default action 
		 * if no $action parameter is passed in the constructor.
		 * The default action used is the WordPress one (-1).
		 */
		return apply_filters( 'inpsyde.nonce.default_action', self::$default_action );
	}

	/**
	 * Getter method to get the key.
	 *
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * Getter method to get the default key.
	 *
	 * @return string
	 */
	public function getDefaultKey() {
		/**
		 * This filter lets user change, via code, the default key
		 * if no $key parameter is passed in the constructor.
		 * The default key used is the WordPress one (_wpnonce).
		 */
		return apply_filters( 'inpsyde.nonce.default_key', self::$default_key );
	}

	/**
	 * Getter method to get the generated nonce.
	 *
	 * @return string
	 */
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
			if ( empty( $action ) 
				|| ! isset( $action[0] ) 
				|| ! isset( $action[1] ) 
				|| ! is_string( $action[0] ) 
				|| ! is_array( $action[1] ) ) {
				return new \WP_Error( 'invalid_arguments', 'When passing an array as an action, please provide a string as first value and an array as second value.', $action );
			}

			$format = $action[0];
			$args   = $action[1];

			return vsprintf( $format, $args );
		}
	}

	/**
	 * Magic method to return the generated $nonce if we echo the object.
	 * Will return the generated $nonce if it was created, or a readable message
	 * if $nonce was not yet created.
	 *
	 * @return string
	 */
	public function __toString() {
		if ( is_null( $this->getNonce() ) ) {
			return 'Please call the create() method first in order to generate the nonce.';
		}
	
		return $this->getNonce();
	}
}