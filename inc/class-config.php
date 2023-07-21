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
	static $highlighter = array(
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
	static $font_size = array( 80, 90, 130, 160 );
}
