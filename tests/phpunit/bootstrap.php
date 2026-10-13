<?php
/**
 * PHPUnit bootstrap file
 *
 * @package richtext-extension
 */

$rtex_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $rtex_tests_dir ) {
	$rtex_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( "{$rtex_tests_dir}/includes/functions.php" ) ) {
	echo "Could not find {$rtex_tests_dir}/includes/functions.php" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', dirname( __DIR__, 2 ) . '/vendor/yoast/phpunit-polyfills' );

require_once "{$rtex_tests_dir}/includes/functions.php";

tests_add_filter(
	'muplugins_loaded',
	static function () {
		require dirname( __DIR__, 2 ) . '/richtext-extension.php';
	}
);

require "{$rtex_tests_dir}/includes/bootstrap.php";
