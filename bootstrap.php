<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Inpsyde_Nonce
 */
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // WPCS: XSS ok.
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	$files = [
		'class-nonce-common.php',
		'class-nonce.php',
		'class-nonce-url.php',
		'class-nonce-field.php',
	];

	array_walk( $files, function( $file ) {
		require_once dirname( __FILE__ ) . "/src/{$file}";
	} );
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require_once $_tests_dir . '/includes/bootstrap.php';

require_once dirname( __FILE__ ) . '/tests/inc/class-nonce-test-case.php';
