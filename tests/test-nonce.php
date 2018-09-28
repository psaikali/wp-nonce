<?php

use Inpsyde\Nonce\Nonce as Nonce;

class Nonce_Test extends WP_UnitTestCase {
	private static $default_action = -1;
	private static $default_key    = '_wpnonce';
	
	/**
	 * Test if our class exists.
	 */
	public function test_class_exists() {
		$this->assertTrue( class_exists( 'Inpsyde\Nonce\Nonce' ) );
	}

	/**
	 * Test that default $action and $key values are used when instantiating without parameters.
	 */
	public function test_construction_without_parameters() {
		$nonce = new Nonce();
		$this->assertSame( self::$default_action, $nonce->getAction() );
		$this->assertSame( self::$default_key, $nonce->getKey() );
	}

	/**
	 * Test the multiple ways of setting an action.
	 */
	public function test_nonce_get_action_parameters_multiformats() {
		// String
		$action_string       = 'action_1_as_a_string';
		$nonce_with_a_string = new Nonce( $action_string );
		$this->assertSame( $action_string, $nonce_with_a_string->getAction() );

		// Array
		$action_array_sprintf  = [ '%s_%d_as_an_%s', 'action', 2, 'array' ];
		$action_array_vsprintf = [ '%s_%d_as_an_%s', [ 'action', 2, 'array' ] ];
		$nonce_with_sprintf    = new Nonce( $action_array_sprintf );
		$nonce_with_vsprintf   = new Nonce( $action_array_vsprintf );
		$this->assertSame( $nonce_with_sprintf->getAction(), $nonce_with_vsprintf->getAction() );

		// Invalid action
		$invalid_action_object = new StdClass();
		$nonce_with_object     = new Nonce( $invalid_action_object );
		$invalid_action_null   = null;
		$nonce_with_null       = new Nonce( $invalid_action_null );
		$this->assertSame( self::$default_action, $nonce_with_object->getAction() );
		$this->assertSame( self::$default_action, $nonce_with_null->getAction() );

		// Filter the default action
		$new_default_action = 'new_default_action';

		add_filter( 'inpsyde.nonce.default_action', function( $default_action ) use ( $new_default_action ) {
			return $new_default_action;
		} );

		$nonce_without_action = new Nonce();
		$this->assertSame( $new_default_action, $nonce_without_action->getAction() );
	}

	/**
	 * Test that using the create() method results in the same as using native WP function,
	 * and that isValid() will pass against the native WP nonce.
	 * @dataProvider provider_actions_for_nonce_creation
	 */
	public function test_nonce_creation( $unformatted_action, $formatted_action ) {
		$wp_nonce     = wp_create_nonce( $formatted_action );
		$tested_nonce = ( new Nonce( $unformatted_action ) );

		$this->assertSame( $wp_nonce, $tested_nonce->create() );
		$this->assertSame( 1, $tested_nonce->isValid( $wp_nonce ) );
	}

	/**
	 * Provider for test_nonce_creation()
	 */
	public function provider_actions_for_nonce_creation() {
		return [
			[ 'abcdefgh', 'abcdefgh' ],
			[ 12345678, 12345678 ],
			[ -1, -1 ],
			[ false, false ],
			[ null, self::$default_action ],
			[ [ ], self::$default_action ],
			[ ( new StdClass() ), self::$default_action ],
			[ [ '%s_%d_as_an_%s', 'action', 1, 'array' ], 'action_1_as_an_array' ]
		];
	}

	/**
	 * Test admin request verification is working properly
	 */
	public function test_admin_request_validity() {
		$action                       = 'abcd1234';
		$custom_key                   = '_my_secret_nonce';
		$admin_nonce_with_default_key = new Nonce( $action );
		$admin_nonce_with_custom_key  = new Nonce( $action, $custom_key );
		$exception                    = null;

		// Fake a nonce stored in the $_REQUEST with default key.
		$_REQUEST[ self::$default_key ] = wp_create_nonce( $action );
		$this->assertSame( 1, $admin_nonce_with_default_key->isValidAdminRequest() );

		// Fake a nonce stored in the $_REQUEST with custom key.
		$_REQUEST[ $custom_key ] = wp_create_nonce( $action );
		$this->assertSame( 1, $admin_nonce_with_custom_key->isValidAdminRequest() );

		unset( $_REQUEST[ self::$default_key ] );

		try {
			$is_valid_admin_request = $admin_nonce_with_default_key->isValidAdminRequest();
		} catch ( Exception $e ) {
			$exception = $e;
		}

		$this->assertInstanceOf( 'WPDieException', $exception, 'After deleting $_REQUEST[\'_wpnonce\'], checking the admin request should throw an exception.' );
	}

	/**
	 * Test AJAX request verification is working properly
	 */
	public function test_ajax_request_validity() {
		$action                       = 'abcd1234';
		$custom_key                   = '_my_secret_nonce';
		$ajax_nonce_with_default_key = new Nonce( $action );
		$ajax_nonce_with_custom_key  = new Nonce( $action, $custom_key );
		$exception                    = null;

		// Fake a nonce stored in the $_REQUEST with default key.
		$_REQUEST[ self::$default_key ] = wp_create_nonce( $action );
		$this->assertSame( 1, $ajax_nonce_with_default_key->isValidAjaxRequest( false ) );

		// Fake a nonce stored in the $_REQUEST with custom key.
		$_REQUEST[ $custom_key ] = wp_create_nonce( $action );
		$this->assertSame( 1, $ajax_nonce_with_custom_key->isValidAjaxRequest( false ) );

		// Unset faked nonces in $_REQUEST.
		unset( $_REQUEST[ self::$default_key ] );
		unset( $_REQUEST[ $custom_key ] );

		$is_valid_ajax_request_with_default_key = $ajax_nonce_with_default_key->isValidAjaxRequest( false );
		$is_valid_ajax_request_with_custom_key  = $ajax_nonce_with_custom_key->isValidAjaxRequest( false );

		$this->assertFalse( $is_valid_ajax_request_with_default_key );
		$this->assertFalse( $is_valid_ajax_request_with_custom_key );
	}
}
