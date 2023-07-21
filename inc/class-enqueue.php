<?php
/**
 * @package richtext-extension
 * @author Aki Hamano
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

		// Add inline CSS to iframe editor instances in WordPress 5.9
		add_filter( 'block_editor_settings_all', array( $this, 'add_iframe_inline_css' ) );
	}

	/**
	 * Enqueue front-end scripts
	 */
	public function enqueue_scripts() {
		wp_register_style( RTEX_NAMESPACE, false );
		wp_enqueue_style( RTEX_NAMESPACE );

		$inline_css = $this->get_inline_css();
		wp_add_inline_style( RTEX_NAMESPACE, $inline_css );
	}

	/**
	 * Enqueue block editor scripts
	 */
	public function enqueue_editor_scripts() {
		wp_register_style( RTEX_NAMESPACE, false );
		wp_enqueue_style( RTEX_NAMESPACE );

		$inline_css  = $this->get_inline_css();
		$inline_css .= '.rtex-dropdown-popover .components-dropdown-menu__menu-item{justify-content:left;height:auto;}';
		$inline_css .= '.rtex-dropdown-popover .components-dropdown-menu__menu-item svg{margin-right:8px;}';

		wp_add_inline_style( RTEX_NAMESPACE, $inline_css );

		$asset = include( RTEX_PATH . '/build/index.asset.php' );
		wp_enqueue_script( RTEX_NAMESPACE, RTEX_URL . '/build/index.js', $asset['dependencies'] );

		wp_localize_script( RTEX_NAMESPACE, 'rtexConf', $this->create_editor_config() );

		wp_set_script_translations( RTEX_NAMESPACE, RTEX_NAMESPACE );
	}

	/**
	 * Enqueue option page scripts
	 */
	public function enqueue_option_scripts( $hook ) {
		if ( 'settings_page_richtext-extension-option' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 'richtext-extension-option', RTEX_URL . '/build/style-index.css', array(), RTEX_VERSION );

		$inline_css = $this->get_inline_css();
		wp_add_inline_style( 'richtext-extension-option', $inline_css );

		wp_enqueue_script( 'wp-color-picker' );
	}

	/**
	 * Add inline CSS to iframe editor instances in WordPress 5.9
	 */
	public function add_iframe_inline_css( $settings ) {
		$inline_css           = $this->get_inline_css();
		$settings['styles'][] = array( 'css' => $inline_css );
		return $settings;
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
				$css_selector = ".rtex-highlighter-${i}, #rtex-highlighter-preview-${i}";
				$thickness    = get_option( 'rtex_highlighter_thickness_' . $i, Config::$highlighter[ $i ]['thickness'] );
				$color_hex    = get_option( 'rtex_highlighter_color_' . $i, Config::$highlighter[ $i ]['color'] );
				$type         = get_option( 'rtex_highlighter_type_' . $i, Config::$highlighter[ $i ]['type'] );
				$color        = 'transparent';

				// Generate rgba value
				if ( preg_match( '/^#[0-9a-fA-F]{6}$/', $color_hex ) ) {
					$opacity = get_option( 'rtex_highlighter_opacity_' . $i, Config::$highlighter[ $i ]['opacity'] ) / 100;
					if ( 1 === $opacity ) {
						$color = $color_hex;
					} else {
						$r     = hexdec( substr( $color_hex, 1, 2 ) );
						$g     = hexdec( substr( $color_hex, 3, 2 ) );
						$b     = hexdec( substr( $color_hex, 5, 2 ) );
						$color = "rgba(${r}, ${g}, ${b}, ${opacity})";
					}
				}

				// Generate gradient value
				if ( 'solid' === $type ) {
					$thickness = 100 - $thickness;
					if ( 0 === $thickness ) {
						$background_value = $color;
					} else {
						$background_value = "linear-gradient(transparent ${thickness}%, ${color} ${thickness}%)";
					}
				} elseif ( 'stripe' === $type ) {
					$background_value = "repeating-linear-gradient(-45deg, ${color} 0, ${color} 3px, transparent 3px, transparent 6px) no-repeat bottom/100% ${thickness}%";
				} elseif ( 'stripe-thin' === $type ) {
					$background_value = "repeating-linear-gradient(-45deg, ${color} 0, ${color} 2px, transparent 2px, transparent 4px) no-repeat bottom/100% ${thickness}%";
				}

				// Generate CSS
				$css .= "$css_selector{background: $background_value;}";
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
			__( 'Solid Marker', 'richtext-extension' ),
			__( 'Striped Marker', 'richtext-extension' ),
			__( 'Solid Background', 'richtext-extension' ),
			__( 'Striped Background', 'richtext-extension' ),
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
