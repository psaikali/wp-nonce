<?php

use Inpsyde\Nonce\Nonce as Nonce;
use Inpsyde\Nonce\Nonce_Field as Nonce_Field;

/**
 * @group nonce-field
 */
class Nonce_Field_Test extends Nonce_Test_Case {
	/**
	 * Test if our class exists.
	 */
	public function test_class_exists() {
		$this->assertTrue( class_exists( 'Inpsyde\Nonce\Nonce_Field' ) );
	}

	/**
	 * Test that creating a Nonce field returns a hidden input field HTML markup 
	 * containing the nonce and the key
	 */
	public function test_creation_returns_a_field_markup_containing_nonce() {
		$nonce_field     = new Nonce_Field( 'some-action', 'some-key' );
		$generated_field = $nonce_field->create();

		$this->assertStringStartsWith( '<input type="hidden"', $generated_field );
		$this->assertContains( $nonce_field->get_nonce(), $generated_field );
		$this->assertContains( $nonce_field->get_key(), $generated_field );
	}
	
	/**
	 * Test that creating a Nonce field will return 2 hidden input fields HTML markup 
	 * if $referer is set to true
	 */
	public function test_creation_returns_two_fields_markup_when_referer() {
		$nonce_field     = new Nonce_Field( 'some-action', 'some-key' );
		$generated_field = $nonce_field->create( true );
		$inputs_count    = substr_count( $generated_field, '<input type="hidden"' );

		$this->assertEquals( 2, $inputs_count );
		$this->assertContains( '_wp_http_referer', $generated_field );
	}

	/**
	 * Test that the is_valid( $value ) method will look for values in REQUEST if $value is not set
	 */
	public function test_creation_validity_without_value() {
		$nonce_field     = new Nonce_Field( 'some-action', 'some-key' );
		$generated_field = $nonce_field->create();

		$_REQUEST[ 'some-key' ] = $nonce_field->get_nonce();

		$this->assertValidNonce( $nonce_field->is_valid() );
	}

	/**
	 * Test that the is_valid( $value ) method will correctly verify a nonce
	 */
	public function test_creation_validity_with_value() {
		$nonce           = new Nonce( 'some-action' );
		$nonce_field     = new Nonce_Field( 'some-action', 'some-key' );
		$generated_nonce = $nonce->create();
		$generated_field = $nonce_field->create();

		$this->assertValidNonce( $nonce_field->is_valid( $generated_nonce ) );
	}
}
