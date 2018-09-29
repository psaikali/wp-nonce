<?php

use Pskli\Nonce\Nonce as Nonce;
use Pskli\Nonce\Nonce_URL as Nonce_URL;

/**
 * @group nonce-url
 */
class Nonce_URL_Test extends Nonce_Test_Case {
	/**
	 * Test if our class exists.
	 */
	public function test_class_exists() {
		$this->assertTrue( class_exists( 'Pskli\Nonce\Nonce_URL' ) );
	}

	/**
	 * Test that creating a Nonce URL returns the URL
	 */
	public function test_creation_returns_a_url() {
		$nonce_url       = new Nonce_URL();
		$generated_url   = $nonce_url->create( self::$url );

		$this->assertNotNull( $generated_url );
		$this->assertNotSame( self::$url, $generated_url );
		$this->assertContains( self::$url, $generated_url );
	}

	/**
	 * Test that creating a Nonce URL returns a URL containing the generated nonce
	 */
	public function test_creation_returns_a_url_containing_a_nonce() {
		$nonce_url       = new Nonce_URL();
		$generated_nonce = $nonce_url->create( self::$url );

		$this->assertContains( $nonce_url->get_nonce(), $generated_nonce );
	}
	
	/**
	 * Test that the is_valid( $value ) method will look for values in URL if $value is not set
	 */
	public function test_creation_validity_without_value() {
		$default_nonce1 = ( new Nonce() )->create();
		$default_nonce2 = ( new Nonce( null, 'different_key' ) )->create();
		$nonce_url1     = new Nonce_URL();
		$nonce_url2     = new Nonce_URL( null, 'different_key' );

		$this->assertFalse( $nonce_url1->is_valid() );
		$this->assertFalse( $nonce_url2->is_valid() );

		$_GET[ self::$default_key ] = $default_nonce1;

		$this->assertValidNonce( $nonce_url1->is_valid() );
		$this->assertFalse( $nonce_url2->is_valid() );

		unset( $_GET[ self::$default_key ] );
		$_GET[ 'different_key' ] = $default_nonce2;

		$this->assertFalse( $nonce_url1->is_valid() );
		$this->assertValidNonce( $nonce_url2->is_valid() );
	}

	/**
	 * Test that the is_valid( $value ) method will correctly verify a nonce
	 */
	public function test_creation_validity_with_value() {
		$default_nonce1       = ( new Nonce() )->create();
		$default_nonce2       = ( new Nonce( 'different_action' ) )->create();
		$generated_nonce_url1 = new Nonce_URL();
		$generated_nonce_url2 = new Nonce_URL( 'different_action', 'different_key' );
		
		$generated_nonce_url1->create( self::$url );

		$this->assertValidNonce( $generated_nonce_url1->is_valid( $default_nonce1 ) );
		$this->assertValidNonce( $generated_nonce_url2->is_valid( $default_nonce2 ) );
		$this->assertFalse( $generated_nonce_url1->is_valid( $default_nonce2 ) );
	}
}
