<?php
/**
 * @package richtext-extension
 * @author Aki Hamano
 * @license GPL-2.0+
 */

namespace richtext_extension;

class Config {
	/**
	 * Default highlighter variation
	 */
	public static $highlighter = array(
		array(
			'color'     => '#ffff66',
			'thickness' => 40,
			'opacity'   => 70,
			'type'      => 'solid',
		),
		array(
			'color'     => '#ff7f7f',
			'thickness' => 40,
			'opacity'   => 40,
			'type'      => 'solid',
		),
		array(
			'color'     => '#ffff66',
			'thickness' => 100,
			'opacity'   => 70,
			'type'      => 'solid',
		),
		array(
			'color'     => '#ff7f7f',
			'thickness' => 100,
			'opacity'   => 40,
			'type'      => 'solid',
		),
	);

	/**
	 * Default font size variation
	 */
	public static $font_size = array( 80, 90, 130, 160 );

	/**
	 * Get default settings
	 *
	 * @return array
	 */
	public static function get_default_settings() {
		$highlighter_titles = array(
			__( 'Marker ( Yellow )', 'richtext-extension' ),
			__( 'Marker ( Red )', 'richtext-extension' ),
			__( 'Background ( Yellow )', 'richtext-extension' ),
			__( 'Background ( Red )', 'richtext-extension' ),
		);

		$font_size_titles = array(
			__( 'Extra small', 'richtext-extension' ),
			__( 'Small', 'richtext-extension' ),
			__( 'Large', 'richtext-extension' ),
			__( 'Extra large', 'richtext-extension' ),
		);

		$settings = array(
			'highlighter'         => array(),
			'font_size'           => array(),
			'underline_active'    => true,
			'clear_format_active' => true,
		);

		foreach ( self::$highlighter as $i => $highlighter ) {
			$settings['highlighter'][] = array_merge(
				array(
					'active' => true,
					'title'  => $highlighter_titles[ $i ],
				),
				$highlighter
			);
		}

		foreach ( self::$font_size as $i => $size ) {
			$settings['font_size'][] = array(
				'active' => true,
				'title'  => $font_size_titles[ $i ],
				'size'   => $size,
			);
		}

		return $settings;
	}
}
