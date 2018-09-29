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
	 * @param string|array $action (optional) An action to be used to generate the nonce.
	 * @param string       $key (optional) A key to be used to "store" the nonce in URL or input field.
	 */
	public function __construct( $action = null, $key = null ) {
		$this->set_action( $action );
		$this->set_key( $key );
	}

	/**
	 * Setter method to set the $action.
	 *
	 * @param string|float|int|array $action
	 * @return string $action The formatted action used to generate the nonce.
	 */
	protected function set_action( $action ) {
		if ( is_scalar( $action ) || is_array( $action ) ) {
			$this->action = $action;
		} else {
			$this->action = $this->get_default_action();
		}

		return $this->get_action();
	}

	/**
	 * Setter method to set the $key.
	 *
	 * @param string $key
	 * @return string $key The key used to access the nonce in the URL or input field.
	 */
	protected function set_key( $key ) {
		if ( is_null( $key ) ) {
			$this->key = $this->get_default_key();
		} else {
			$this->key = $key;
		}

		return $this->get_key();
	}

	/**
	 * Setter method to set the $nonce.
	 *
	 * @param string $nonce
	 * @return string $nonce The generated nonce.
	 */
	protected function set_nonce( string $nonce ) {
		$this->nonce = $nonce;
		return $this->get_nonce();
	}

	/**
	 * Getter method to get the action and format it.
	 *
	 * @return string $action The formatted action used to generate the nonce.
	 */
	public function get_action() {
		return ( is_null( $this->action ) ) ? $this->get_default_action() : $this->format_action( $this->action );
	}

	/**
	 * Getter method to get the default action.
	 *
	 * @return string $default_action The default action used if no $action is passed in the constructor.
	 */
	public function get_default_action() {
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
	 * @return string $key The key used to access the nonce in the URL or input field.
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * Getter method to get the default key.
	 *
	 * @return string $default_key The default key used if no $key is passed in the constructor.
	 */
	public function get_default_key() {
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
	 * @return string $nonce The generated nonce.
	 */
	public function get_nonce() {
		return $this->nonce;
	}

	/**
	 * Format an action by giving a simple string,
	 * or an array like [ 'action_%s_%d', [ 'string', 123 ] ] to generate a dynamic action string using vsprintf()
	 *
	 * @param string|array ...$action The action to format: a simple string, or an array to use in a sprintf() or vsprintf() manner to render a dynamic string.
	 * @return string Formatted action string if passed $action parameter is valid, or default action if parameter is invalid.
	 */
	protected function format_action( ...$action ) {
		$action = $action[0];

		if ( is_scalar( $action ) ) {
			return $action;
		}

		if ( is_array( $action ) && ! empty( $action ) ) {
			$format = array_shift( $action );

			/**
			 * Simple hack to allow using vsprintf()-or-sprintf()-like formats, so that
			 * [ 'placeholder_%1$s_%2$d, [ 'string', 1234 ] ] and
			 * [ 'placeholder_%1$s_%2$d, 'string', 1234 ] will work the same.
			 */
			$args = is_array( $action[0] ) ? $action[0] : $action;

			return vsprintf( $format, $args );
		}

		return $this->get_default_action();
	}

	/**
	 * Magic method to return the generated $nonce if we echo the object.
	 * Will return the generated $nonce if it was created, or a readable message
	 * if $nonce was not yet created.
	 *
	 * @return string $message|$nonce
	 */
	public function __toString() {
		if ( is_null( $this->get_nonce() ) ) {
			return 'Please call the create() method first in order to generate the nonce.';
		}

		return $this->get_nonce();
	}
}
