<?php
/**
 * Tests for the Options class
 *
 * @package richtext-extension
 */

use richtext_extension\Options;

class Options_Test extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
		delete_option( Options::OPTION_NAME );
	}

	/**
	 * Call the private `Options::migrate_legacy_options()` method.
	 */
	private function migrate_legacy_options() {
		$method = new ReflectionMethod( Options::class, 'migrate_legacy_options' );

		// Private methods are invokable without this since PHP 8.1.
		if ( PHP_VERSION_ID < 80100 ) {
			$method->setAccessible( true );
		}

		$method->invoke( null );
	}

	public function test_sanitize_returns_defaults_for_non_array_value() {
		$this->assertSame( Options::get_default_settings(), Options::sanitize( 'invalid' ) );
		$this->assertSame( Options::get_default_settings(), Options::sanitize( false ) );
	}

	public function test_sanitize_fills_in_missing_values() {
		$defaults = Options::get_default_settings();
		$settings = Options::sanitize(
			array(
				'highlighter' => array(
					array( 'color' => '#000000' ),
				),
				'font_size'   => array(
					1 => array( 'active' => false ),
				),
			)
		);

		$this->assertSame( array_merge( $defaults['highlighter'][0], array( 'color' => '#000000' ) ), $settings['highlighter'][0] );
		$this->assertSame( $defaults['highlighter'][1], $settings['highlighter'][1] );
		$this->assertSame( array_merge( $defaults['font_size'][1], array( 'active' => false ) ), $settings['font_size'][1] );
		$this->assertSame( $defaults['font_size'][0], $settings['font_size'][0] );
		$this->assertTrue( $settings['underline_active'] );
		$this->assertTrue( $settings['clear_format_active'] );
	}

	public function test_sanitize_removes_unknown_keys() {
		$settings = Options::sanitize(
			array(
				'highlighter' => array(
					array( 'unknown' => 'value' ),
				),
				'unknown'     => 'value',
			)
		);

		$this->assertArrayNotHasKey( 'unknown', $settings['highlighter'][0] );
		$this->assertArrayNotHasKey( 'unknown', $settings );
	}

	/**
	 * @dataProvider data_sanitize_clamps_highlighter_range
	 */
	public function test_sanitize_clamps_highlighter_range( $value, $expected ) {
		$settings = Options::sanitize(
			array(
				'highlighter' => array(
					array(
						'thickness' => $value,
						'opacity'   => $value,
					),
				),
			)
		);

		$this->assertSame( $expected, $settings['highlighter'][0]['thickness'] );
		$this->assertSame( $expected, $settings['highlighter'][0]['opacity'] );
	}

	public function data_sanitize_clamps_highlighter_range() {
		return array(
			'below minimum' => array( -10, 0 ),
			'minimum'       => array( 0, 0 ),
			'in range'      => array( 50, 50 ),
			'numeric text'  => array( '30', 30 ),
			'maximum'       => array( 100, 100 ),
			'above maximum' => array( 150, 100 ),
		);
	}

	/**
	 * @dataProvider data_sanitize_font_size
	 */
	public function test_sanitize_font_size( $value, $expected ) {
		$settings = Options::sanitize(
			array(
				'font_size' => array(
					array( 'size' => $value ),
				),
			)
		);

		$this->assertSame( $expected, $settings['font_size'][0]['size'] );
	}

	public function data_sanitize_font_size() {
		return array(
			'em in range'     => array( '1.5em', '1.5em' ),
			'em below range'  => array( '0.5em', '0.8em' ),
			'em above range'  => array( '10em', '6em' ),
			'rem in range'    => array( '2rem', '2rem' ),
			'rem above range' => array( '7rem', '6rem' ),
			'px in range'     => array( '16px', '16px' ),
			'px below range'  => array( '8px', '10px' ),
			'px above range'  => array( '100px', '72px' ),
			'rounded'         => array( '1.234em', '1.23em' ),
			'invalid unit'    => array( '12pt', '0.8em' ),
			'without unit'    => array( '12', '0.8em' ),
			'negative'        => array( '-1em', '0.8em' ),
			'not a string'    => array( 12, '0.8em' ),
		);
	}

	/**
	 * @dataProvider data_sanitize_invalid_color
	 */
	public function test_sanitize_invalid_color( $value ) {
		$settings = Options::sanitize(
			array(
				'highlighter' => array(
					1 => array( 'color' => $value ),
				),
			)
		);

		$this->assertSame( '#ff7f7f', $settings['highlighter'][1]['color'] );
	}

	public function data_sanitize_invalid_color() {
		return array(
			'empty'          => array( '' ),
			'short hex'      => array( '#fff' ),
			'without hash'   => array( 'ffffff' ),
			'invalid digits' => array( '#gggggg' ),
			'color name'     => array( 'red' ),
			'not a string'   => array( 123456 ),
		);
	}

	public function test_sanitize_keeps_valid_color() {
		$settings = Options::sanitize(
			array(
				'highlighter' => array(
					array( 'color' => '#A1b2C3' ),
				),
			)
		);

		$this->assertSame( '#A1b2C3', $settings['highlighter'][0]['color'] );
	}

	public function test_sanitize_highlighter_type() {
		$settings = Options::sanitize(
			array(
				'highlighter' => array(
					array( 'type' => 'stripe' ),
					array( 'type' => 'stripe-thin' ),
					array( 'type' => 'dotted' ),
					array( 'type' => null ),
				),
			)
		);

		$this->assertSame( 'stripe', $settings['highlighter'][0]['type'] );
		$this->assertSame( 'stripe-thin', $settings['highlighter'][1]['type'] );
		$this->assertSame( 'solid', $settings['highlighter'][2]['type'] );
		$this->assertSame( 'solid', $settings['highlighter'][3]['type'] );
	}

	public function test_sanitize_booleans_and_titles() {
		$settings = Options::sanitize(
			array(
				'highlighter'         => array(
					array(
						'active' => 'false',
						'title'  => '<b>Title</b>',
					),
				),
				'underline_active'    => 'false',
				'clear_format_active' => 0,
			)
		);

		$this->assertFalse( $settings['highlighter'][0]['active'] );
		$this->assertSame( 'Title', $settings['highlighter'][0]['title'] );
		$this->assertFalse( $settings['underline_active'] );
		$this->assertFalse( $settings['clear_format_active'] );
	}

	public function test_migrate_legacy_options() {
		update_option( 'rtex_highlighter_color_0', '#000000' );
		update_option( 'rtex_highlighter_thickness_0', 50 );
		update_option( 'rtex_highlighter_type_0', 'stripe' );
		update_option( 'rtex_font_size_size_0', '120' );
		update_option( 'rtex_font_size_size_3', '250' );
		// `false` is stored as an empty string in the database.
		update_option( 'rtex_underline_active', '' );

		$this->migrate_legacy_options();

		$settings = get_option( Options::OPTION_NAME );

		$this->assertSame( '#000000', $settings['highlighter'][0]['color'] );
		$this->assertSame( 50, $settings['highlighter'][0]['thickness'] );
		$this->assertSame( 'stripe', $settings['highlighter'][0]['type'] );
		$this->assertSame( '1.2em', $settings['font_size'][0]['size'] );
		$this->assertSame( '0.9em', $settings['font_size'][1]['size'] );
		$this->assertSame( '2.5em', $settings['font_size'][3]['size'] );
		$this->assertFalse( $settings['underline_active'] );
		$this->assertTrue( $settings['clear_format_active'] );
	}

	public function test_migrate_legacy_options_makes_invalid_color_transparent() {
		update_option( 'rtex_highlighter_color_1', '' );
		update_option( 'rtex_highlighter_color_2', 'invalid' );

		$this->migrate_legacy_options();

		$settings = get_option( Options::OPTION_NAME );

		$this->assertSame( 0, $settings['highlighter'][1]['opacity'] );
		$this->assertSame( '#ff7f7f', $settings['highlighter'][1]['color'] );
		$this->assertSame( 0, $settings['highlighter'][2]['opacity'] );
		$this->assertSame( '#ffff66', $settings['highlighter'][2]['color'] );
		$this->assertSame( 70, $settings['highlighter'][0]['opacity'] );
	}

	public function test_migrate_legacy_options_deletes_legacy_options() {
		update_option( 'rtex_highlighter_color_0', '#000000' );
		update_option( 'rtex_font_size_size_0', '120' );
		update_option( 'rtex_clear_format_active', true );

		$this->migrate_legacy_options();

		$this->assertNull( get_option( 'rtex_highlighter_color_0', null ) );
		$this->assertNull( get_option( 'rtex_font_size_size_0', null ) );
		$this->assertNull( get_option( 'rtex_clear_format_active', null ) );
	}

	public function test_migrate_legacy_options_does_nothing_without_legacy_options() {
		$this->migrate_legacy_options();

		$this->assertNull( get_option( Options::OPTION_NAME, null ) );
	}

	public function test_migrate_legacy_options_skips_when_already_migrated() {
		$settings                         = Options::get_default_settings();
		$settings['font_size'][0]['size'] = '2em';
		update_option( Options::OPTION_NAME, $settings );
		update_option( 'rtex_font_size_size_0', '120' );

		$this->migrate_legacy_options();

		$this->assertSame( '2em', get_option( Options::OPTION_NAME )['font_size'][0]['size'] );
		$this->assertSame( '120', get_option( 'rtex_font_size_size_0' ) );
	}
}
