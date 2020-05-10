<?php
/**
 * @package richtext-extension
 * @author Tetsuaki Hamano
 * @license GPL-2.0+
 */

namespace richtext_extension;

class Enqueue {

	/**
	 * Constructor
	 */
	function __construct() {
		// Enqueue front-end scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Enqueue block editor scripts
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_scripts' ) );

		// Enqueue option page scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_option_scripts' ) );
	}

	/**
	 * Enqueue front-end scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'richtext-extension', RTEX_URL . '/css/style.css', array(), RTEX_VERSION );

		$inline_css = $this->get_inline_css();
		wp_add_inline_style( 'richtext-extension', $inline_css );
	}

	/**
	 * Enqueue block editor scripts
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_style( 'richtext-extension-editor', RTEX_URL . '/css/style-editor.css', array(), RTEX_VERSION );

		$inline_css = $this->get_inline_css();
		wp_add_inline_style( 'richtext-extension-editor', $inline_css );

		$asset = include( RTEX_PATH . '/js/format.asset.php' );
		wp_enqueue_script( 'richtext-extension-editor', RTEX_URL . '/js/format.js', $asset['dependencies'] );

		wp_localize_script( 'richtext-extension-editor', 'rtexConf', $this->create_editor_config() );

		wp_set_script_translations( 'richtext-extension-editor', 'richtext-extension', RTEX_PATH . '/languages' );
	}

	/**
	 * Enqueue option page scripts
	 */
	public function enqueue_option_scripts( $hook ) {
		if ( 'settings_page_richtext-extension-option' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 'richtext-extension-option', RTEX_URL . '/css/style-option.css', array(), RTEX_VERSION );

		$inline_css = $this->get_inline_css();
		wp_add_inline_style( 'richtext-extension-option', $inline_css );

		wp_enqueue_script( 'wp-color-picker' );
	}

		/**
	 * Get inline style css
	 *
	 * @return string
	 */
	private function get_inline_css() {
		$css    = '';
		$styles = array();

		// Generate highlighter style
		for ( $i = 0; $i <= 3; $i++ ) {
			if ( get_option( 'rtex_highlighter_active_' . $i, true ) ) {
				$thickness  = 100 - get_option( 'rtex_highlighter_thickness_' . $i, Config::$highlighter[ $i ]['thickness'] );
				$color      = get_option( 'rtex_highlighter_color_' . $i, Config::$highlighter[ $i ]['color'] );
				$color_rgba = 'transparent';

				// Generate linear-gradient value
				if ( preg_match( '/^#[0-9a-fA-F]{6}$/', $color ) ) {
					$r          = hexdec( substr( $color, 1, 2 ) );
					$g          = hexdec( substr( $color, 3, 2 ) );
					$b          = hexdec( substr( $color, 5, 2 ) );
					$opacity    = get_option( 'rtex_highlighter_opacity_' . $i, Config::$highlighter[ $i ]['opacity'] ) / 100;
					$color_rgba = "rgba(${r}, ${g}, ${b}, ${opacity})";
				}

				$css .= ".rtex-highlighter-${i}, #rtex-highlighter-preview-${i}{ background: linear-gradient(transparent ${thickness}%, ${color_rgba} ${thickness}%);}";
			}
		}

		// Generate font size style
		for ( $i = 0; $i <= 3; $i++ ) {
			if ( get_option( 'rtex_font_size_active_' . $i, true ) ) {
				$font_size = get_option( 'rtex_font_size_size_' . $i, Config::$font_size[ $i ] ) / 100;
				$css      .= ".rtex-font-size-${i}, #rtex-font-size-preview-${i}{ font-size: ${font_size}em;}";
			}
		}

		return $css;
	}

	/**
	 * Generate settings to be passed to the block editor
	 *
	 * @return array
	 */
	private function create_editor_config() {
		$config = array(
			'highlighter' => array(),
			'fontSize'    => array(),
		);

		$default_title = array(
			__( 'Marker (Yellow)', 'richtext-extension' ),
			__( 'Marker (Red)', 'richtext-extension' ),
			__( 'Background (Yellow)', 'richtext-extension' ),
			__( 'Background (Red)', 'richtext-extension' ),
		);

		for ( $i = 0; $i <= 3; $i++ ) {
			if ( get_option( 'rtex_highlighter_active_' . $i, true ) ) {
				$config['highlighter'][] = array(
					'title'     => get_option( 'rtex_highlighter_title_' . $i, $default_title[ $i ] ),
					'className' => 'rtex-highlighter-' . $i,
				);
			}
		}

		$default_title = array(
			__( 'Extra small', 'richtext-extension' ),
			__( 'Small', 'richtext-extension' ),
			__( 'Large', 'richtext-extension' ),
			__( 'Extra large', 'richtext-extension' ),
		);

		for ( $i = 0; $i <= 3; $i++ ) {
			if ( get_option( 'rtex_font_size_active_' . $i, true ) ) {
				$config['fontSize'][] = array(
					'title'     => get_option( 'rtex_font_size_title_' . $i, $default_title[ $i ] ),
					'className' => 'rtex-font-size-' . $i,
				);
			}
		}

		$config['underlineActive']   = get_option( 'rtex_underline_active', true );
		$config['clearFormatActive'] = get_option( 'rtex_clear_format_active', true );

		return $config;
	}
}

new Enqueue();
