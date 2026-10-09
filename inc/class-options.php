<?php
/**
 * @package richtext-extension
 * @author Aki Hamano
 * @license GPL-2.0+
 */

namespace richtext_extension;

class Options {
	/**
	 * Option name to store all plugin settings as a single object
	 */
	const OPTION_NAME = 'rtex_settings';

	/**
	 * Option name to store the plugin version that the stored settings correspond to
	 */
	const VERSION_OPTION_NAME = 'rtex_version';

	/**
	 * Settable font size range
	 */
	const MIN_FONT_SIZE = 80;
	const MAX_FONT_SIZE = 300;

	/**
	 * Allowed highlighter types
	 */
	const HIGHLIGHTER_TYPES = array( 'solid', 'stripe', 'stripe-thin' );

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add option page
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		// Register setting on `init` so that it is available in both admin screens and the REST API
		add_action( 'init', array( $this, 'register_option' ) );

		// Normalize the stored settings when reading them, so that an invalid stored value
		// (e.g. edited directly in the database) does not break the REST API response
		add_filter( 'option_' . self::OPTION_NAME, array( __CLASS__, 'sanitize' ) );

		// Migrate stored settings after the setting is registered
		add_action( 'init', array( $this, 'maybe_upgrade' ), 11 );
	}

	/**
	 * Add option page
	 */
	public function add_options_page() {
		add_options_page(
			__( 'RichText Extension Setting', 'richtext-extension' ),
			__( 'RichText Extension', 'richtext-extension' ),
			'manage_options',
			'richtext-extension-option',
			array( $this, 'create_options_page' )
		);
	}

	/**
	 * Create option page
	 */
	public function create_options_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'RichText Extension Setting', 'richtext-extension' ); ?></h1>
			<hr class="wp-header-end">
			<div id="rtex-settings"></div>
		</div>
		<?php
	}

	/**
	 * Create setting
	 */
	public function register_option() {
		register_setting(
			'richtext-extension',
			self::OPTION_NAME,
			array(
				'type'              => 'object',
				'label'             => __( 'RichText Extension Settings', 'richtext-extension' ),
				'default'           => Config::get_default_settings(),
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'show_in_rest'      => array(
					'schema' => self::get_schema(),
				),
			)
		);
	}

	/**
	 * Get the REST API schema of the setting
	 *
	 * @return array
	 */
	private static function get_schema() {
		$default_settings = Config::get_default_settings();

		return array(
			'type'                 => 'object',
			'additionalProperties' => false,
			'properties'           => array(
				'highlighter'         => array(
					'type'     => 'array',
					'minItems' => count( $default_settings['highlighter'] ),
					'maxItems' => count( $default_settings['highlighter'] ),
					'items'    => array(
						'type'                 => 'object',
						'additionalProperties' => false,
						'properties'           => array(
							'active'    => array(
								'type' => 'boolean',
							),
							'title'     => array(
								'type' => 'string',
							),
							'color'     => array(
								'type'    => 'string',
								'pattern' => '^#[0-9a-fA-F]{6}$',
							),
							'thickness' => array(
								'type'    => 'integer',
								'minimum' => 0,
								'maximum' => 100,
							),
							'opacity'   => array(
								'type'    => 'integer',
								'minimum' => 0,
								'maximum' => 100,
							),
							'type'      => array(
								'type' => 'string',
								'enum' => self::HIGHLIGHTER_TYPES,
							),
						),
					),
				),
				'font_size'           => array(
					'type'     => 'array',
					'minItems' => count( $default_settings['font_size'] ),
					'maxItems' => count( $default_settings['font_size'] ),
					'items'    => array(
						'type'                 => 'object',
						'additionalProperties' => false,
						'properties'           => array(
							'active' => array(
								'type' => 'boolean',
							),
							'title'  => array(
								'type' => 'string',
							),
							'size'   => array(
								'type'    => 'integer',
								'minimum' => self::MIN_FONT_SIZE,
								'maximum' => self::MAX_FONT_SIZE,
							),
						),
					),
				),
				'underline_active'    => array(
					'type' => 'boolean',
				),
				'clear_format_active' => array(
					'type' => 'boolean',
				),
			),
		);
	}

	/**
	 * Get settings
	 *
	 * @return array
	 */
	public static function get_settings() {
		return self::sanitize( get_option( self::OPTION_NAME ) );
	}

	/**
	 * Sanitizer
	 *
	 * Fills in missing values with the defaults so that the settings always have the complete structure.
	 *
	 * @param mixed $value input value.
	 *
	 * @return array
	 */
	public static function sanitize( $value ) {
		$value    = is_array( $value ) ? $value : array();
		$settings = Config::get_default_settings();

		foreach ( $settings['highlighter'] as $i => $default_item ) {
			$item = self::get_item( $value, 'highlighter', $i, $default_item );

			$settings['highlighter'][ $i ] = array(
				'active'    => rest_sanitize_boolean( $item['active'] ),
				'title'     => sanitize_text_field( $item['title'] ),
				'color'     => self::sanitize_color( $item['color'], $default_item['color'] ),
				'thickness' => self::sanitize_range( $item['thickness'], 0, 100 ),
				'opacity'   => self::sanitize_range( $item['opacity'], 0, 100 ),
				'type'      => in_array( $item['type'], self::HIGHLIGHTER_TYPES, true ) ? $item['type'] : $default_item['type'],
			);
		}

		foreach ( $settings['font_size'] as $i => $default_item ) {
			$item = self::get_item( $value, 'font_size', $i, $default_item );

			$settings['font_size'][ $i ] = array(
				'active' => rest_sanitize_boolean( $item['active'] ),
				'title'  => sanitize_text_field( $item['title'] ),
				'size'   => self::sanitize_range( $item['size'], self::MIN_FONT_SIZE, self::MAX_FONT_SIZE ),
			);
		}

		foreach ( array( 'underline_active', 'clear_format_active' ) as $key ) {
			if ( isset( $value[ $key ] ) ) {
				$settings[ $key ] = rest_sanitize_boolean( $value[ $key ] );
			}
		}

		return $settings;
	}

	/**
	 * Get an item of a list setting merged with the defaults
	 *
	 * @param array  $value        input value.
	 * @param string $key          setting key.
	 * @param int    $index        item index.
	 * @param array  $default_item default item.
	 *
	 * @return array
	 */
	private static function get_item( $value, $key, $index, $default_item ) {
		$item = $value[ $key ][ $index ] ?? array();
		return is_array( $item ) ? array_merge( $default_item, array_intersect_key( $item, $default_item ) ) : $default_item;
	}

	/**
	 * Sanitizer (Color)
	 *
	 * @param mixed  $value         input value.
	 * @param string $default_value default value.
	 *
	 * @return string
	 */
	private static function sanitize_color( $value, $default_value ) {
		return is_string( $value ) && preg_match( '/^#[0-9a-fA-F]{6}$/', $value ) ? $value : $default_value;
	}

	/**
	 * Sanitizer (Range)
	 *
	 * @param mixed $value input value.
	 * @param int   $min   minimum value.
	 * @param int   $max   maximum value.
	 *
	 * @return int
	 */
	private static function sanitize_range( $value, $min, $max ) {
		return min( max( (int) $value, $min ), $max );
	}

	/**
	 * Migrate stored settings to the latest structure
	 */
	public function maybe_upgrade() {
		$version = get_option( self::VERSION_OPTION_NAME, '0' );

		if ( version_compare( $version, RTEX_VERSION, '>=' ) ) {
			return;
		}

		if ( version_compare( $version, '3.2.0', '<' ) ) {
			self::migrate_legacy_options();
		}

		update_option( self::VERSION_OPTION_NAME, RTEX_VERSION );
	}

	/**
	 * Migrate the individual options used up to version 3.1.0 to a single option
	 */
	private static function migrate_legacy_options() {
		$settings            = Config::get_default_settings();
		$legacy_option_names = array();

		$get_legacy_option = static function ( $option_name, $default_value ) use ( &$legacy_option_names ) {
			$value = get_option( $option_name, null );
			if ( null === $value ) {
				return $default_value;
			}
			$legacy_option_names[] = $option_name;
			return $value;
		};

		// Legacy option names are built from the keys of the new structure, e.g. `rtex_highlighter_color_0`.
		foreach ( array( 'highlighter', 'font_size' ) as $key ) {
			foreach ( $settings[ $key ] as $i => $item ) {
				foreach ( $item as $item_key => $default_value ) {
					$settings[ $key ][ $i ][ $item_key ] = $get_legacy_option( "rtex_{$key}_{$item_key}_{$i}", $default_value );
				}
			}
		}

		foreach ( array( 'underline_active', 'clear_format_active' ) as $key ) {
			$settings[ $key ] = $get_legacy_option( "rtex_{$key}", $settings[ $key ] );
		}

		if ( empty( $legacy_option_names ) ) {
			return;
		}

		update_option( self::OPTION_NAME, $settings );

		foreach ( $legacy_option_names as $option_name ) {
			delete_option( $option_name );
		}
	}
}

new Options();
