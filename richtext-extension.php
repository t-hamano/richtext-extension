<?php
/**
 * Plugin Name: RichText Extension
 * Description: Adds useful decoration features to the Gutenberg RichText editor toolbar.
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Version: 2.6.0
 * Author: Aki Hamano
 * Author URI: https://github.com/t-hamano
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: richtext-extension
 * @package richtext-extension
 * @author Aki Hamano
 * @license GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rtex_data = get_file_data(
	__FILE__,
	array(
		'Version' => 'Version',
	)
);

define( 'RTEX_VERSION', $rtex_data['Version'] );
define( 'RTEX_NAMESPACE', 'richtext-extension' );
define( 'RTEX_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'RTEX_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'RTEX_BASENAME', plugin_basename( __FILE__ ) );

require_once RTEX_PATH . '/inc/class-main.php';

new richtext_extension\Main();
