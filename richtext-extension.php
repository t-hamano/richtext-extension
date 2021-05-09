<?php
/**
 * Plugin Name: RichText Extension
 * Description: Adds useful decoration features to the Gutenberg RichText editor toolbar.
 * Version: 1.1.9
 * Author: Tetsuaki Hamano
 * Author URI: https://github.com/t-hamano
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: richtext-extension
 * Domain Path: languages
 * @package richtext-extension
 * @author Tetsuaki Hamano
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
define( 'RTEX_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'RTEX_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'RTEX_BASENAME', plugin_basename( __FILE__ ) );

require_once RTEX_PATH . '/inc/class-main.php';

new richtext_extension\Main();
