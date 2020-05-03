<?php
/**
 * Plugin Name: RichText Extension
 * Description: Adds useful decoration features to the Gutenberg RichText editor toolbar.
 * Version: 0.0.2
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

define( 'RTEX_VERSION', '0.0.2' );
define( 'RTEX_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'RTEX_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'RTEX_BASENAME', plugin_basename( __FILE__ ) );

// Default highlighter variation
$rtex_config['highlighter'] = array(
	array(
		'color'     => '#ffff66',
		'thickness' => 40,
		'opacity'   => 80,
	),
	array(
		'color'     => '#ff7f7f',
		'thickness' => 40,
		'opacity'   => 80,
	),
	array(
		'color'     => '#ffff66',
		'thickness' => 100,
		'opacity'   => 80,
	),
	array(
		'color'     => '#ff7f7f',
		'thickness' => 100,
		'opacity'   => 80,
	),
);

// Default font size variation
$rtex_config['font_size'] = array( 80, 90, 130, 160 );

require_once RTEX_PATH . '/inc/class-main.php';

new richtext_extension\Main();
