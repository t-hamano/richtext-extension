<?php
/**
 * Tests for the Enqueue class
 *
 * @package richtext-extension
 */

use richtext_extension\Enqueue;
use richtext_extension\Options;

class Enqueue_Test extends WP_UnitTestCase {

	/**
	 * Call the private `Enqueue::get_inline_css()` method with the given settings.
	 *
	 * @param array $settings settings.
	 *
	 * @return string
	 */
	private function get_inline_css( $settings ) {
		update_option( Options::OPTION_NAME, $settings );
		rtex_reset_settings_cache();

		$reflection = new ReflectionClass( Enqueue::class );
		$method     = $reflection->getMethod( 'get_inline_css' );

		// Private methods are invokable without this since PHP 8.1.
		if ( PHP_VERSION_ID < 80100 ) {
			$method->setAccessible( true );
		}

		return $method->invoke( $reflection->newInstanceWithoutConstructor() );
	}

	/**
	 * @dataProvider data_get_inline_css_highlighter
	 */
	public function test_get_inline_css_highlighter( $highlighter, $expected ) {
		$settings = Options::get_default_settings();

		// Only output the first highlighter.
		$settings['highlighter'][0] = array_merge( $settings['highlighter'][0], $highlighter );
		foreach ( array( 1, 2, 3 ) as $i ) {
			$settings['highlighter'][ $i ]['active'] = false;
		}
		foreach ( array_keys( $settings['font_size'] ) as $i ) {
			$settings['font_size'][ $i ]['active'] = false;
		}

		$this->assertSame( $expected, $this->get_inline_css( $settings ) );
	}

	public function data_get_inline_css_highlighter() {
		return array(
			'solid'                => array(
				array(
					'type'      => 'solid',
					'color'     => '#ffff66',
					'thickness' => 40,
					'opacity'   => 70,
				),
				'.rtex-highlighter-0{background: linear-gradient(transparent 60%, #ffff66b3 60%);}',
			),
			'stripe'               => array(
				array(
					'type'      => 'stripe',
					'color'     => '#ff7f7f',
					'thickness' => 40,
					'opacity'   => 40,
				),
				'.rtex-highlighter-0{background: repeating-linear-gradient(-45deg, #ff7f7f66 0, #ff7f7f66 3px, transparent 3px, transparent 6px) no-repeat bottom/100% 40%;}',
			),
			'stripe-thin'          => array(
				array(
					'type'      => 'stripe-thin',
					'color'     => '#ff7f7f',
					'thickness' => 40,
					'opacity'   => 40,
				),
				'.rtex-highlighter-0{background: repeating-linear-gradient(-45deg, #ff7f7f66 0, #ff7f7f66 2px, transparent 2px, transparent 4px) no-repeat bottom/100% 40%;}',
			),
			'solid, thickness 100' => array(
				array(
					'type'      => 'solid',
					'color'     => '#ffff66',
					'thickness' => 100,
					'opacity'   => 70,
				),
				'.rtex-highlighter-0{background: #ffff66b3;}',
			),
			'solid, opacity 100'   => array(
				array(
					'type'      => 'solid',
					'color'     => '#ffff66',
					'thickness' => 40,
					'opacity'   => 100,
				),
				'.rtex-highlighter-0{background: linear-gradient(transparent 60%, #ffff66ff 60%);}',
			),
			'stripe, opacity 100'  => array(
				array(
					'type'      => 'stripe',
					'color'     => '#ff7f7f',
					'thickness' => 100,
					'opacity'   => 100,
				),
				'.rtex-highlighter-0{background: repeating-linear-gradient(-45deg, #ff7f7fff 0, #ff7f7fff 3px, transparent 3px, transparent 6px) no-repeat bottom/100% 100%;}',
			),
			'inactive'             => array(
				array( 'active' => false ),
				'',
			),
		);
	}

	public function test_get_inline_css_font_size() {
		$settings = Options::get_default_settings();
		foreach ( array_keys( $settings['highlighter'] ) as $i ) {
			$settings['highlighter'][ $i ]['active'] = false;
		}
		$settings['font_size'][1]['active'] = false;

		$this->assertSame(
			'.rtex-font-size-0{ font-size: 0.8em;}.rtex-font-size-2{ font-size: 1.3em;}.rtex-font-size-3{ font-size: 1.6em;}',
			$this->get_inline_css( $settings )
		);
	}
}
