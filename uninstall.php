<?php
/**
 * @package richtext-extension
 * @author Aki Hamano
 * @license GPL-2.0+
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

$options = array();

for ( $i = 0; $i <= 3; $i++ ) {
	$options[] = 'rtex_highlighter_active_' . $i;
	$options[] = 'rtex_highlighter_title_' . $i;
	$options[] = 'rtex_highlighter_color_' . $i;
	$options[] = 'rtex_highlighter_thickness_' . $i;
	$options[] = 'rtex_highlighter_opacity_' . $i;
	$options[] = 'rtex_highlighter_type_' . $i;
}

for ( $i = 0; $i <= 3; $i++ ) {
	$options[] = 'rtex_font_size_active_' . $i;
	$options[] = 'rtex_font_size_title_' . $i;
	$options[] = 'rtex_font_size_size_' . $i;
}

$options[] = 'rtex_underline_active';
$options[] = 'rtex_clear_format_active';

foreach ( $options as $key ) {
	delete_option( $key );
}
