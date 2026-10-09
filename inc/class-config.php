<?php
/**
 * @package richtext-extension
 * @author Aki Hamano
 * @license GPL-2.0+
 */

namespace richtext_extension;

class Config {
	/**
	 * Get default settings
	 *
	 * @return array
	 */
	public static function get_default_settings() {
		return array(
			'highlighter'         => array(
				array(
					'active'    => true,
					'title'     => __( 'Marker ( Yellow )', 'richtext-extension' ),
					'color'     => '#ffff66',
					'thickness' => 40,
					'opacity'   => 70,
					'type'      => 'solid',
				),
				array(
					'active'    => true,
					'title'     => __( 'Marker ( Red )', 'richtext-extension' ),
					'color'     => '#ff7f7f',
					'thickness' => 40,
					'opacity'   => 40,
					'type'      => 'solid',
				),
				array(
					'active'    => true,
					'title'     => __( 'Background ( Yellow )', 'richtext-extension' ),
					'color'     => '#ffff66',
					'thickness' => 100,
					'opacity'   => 70,
					'type'      => 'solid',
				),
				array(
					'active'    => true,
					'title'     => __( 'Background ( Red )', 'richtext-extension' ),
					'color'     => '#ff7f7f',
					'thickness' => 100,
					'opacity'   => 40,
					'type'      => 'solid',
				),
			),
			'font_size'           => array(
				array(
					'active' => true,
					'title'  => __( 'Extra small', 'richtext-extension' ),
					'size'   => 80,
				),
				array(
					'active' => true,
					'title'  => __( 'Small', 'richtext-extension' ),
					'size'   => 90,
				),
				array(
					'active' => true,
					'title'  => __( 'Large', 'richtext-extension' ),
					'size'   => 130,
				),
				array(
					'active' => true,
					'title'  => __( 'Extra large', 'richtext-extension' ),
					'size'   => 160,
				),
			),
			'underline_active'    => true,
			'clear_format_active' => true,
		);
	}
}
