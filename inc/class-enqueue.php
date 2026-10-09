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
	public function __construct() {
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

		wp_add_inline_style( RTEX_NAMESPACE, $inline_css );

		$asset = include RTEX_PATH . '/build/index.asset.php';
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

		$asset = include RTEX_PATH . '/build/settings.asset.php';

		wp_enqueue_script(
			RTEX_NAMESPACE . '-settings',
			RTEX_URL . '/build/settings.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( RTEX_NAMESPACE . '-settings', RTEX_NAMESPACE );

		wp_enqueue_style(
			RTEX_NAMESPACE . '-settings',
			RTEX_URL . '/build/style-settings.css',
			array( 'wp-components', 'wp-theme' ),
			$asset['version']
		);
		wp_style_add_data( RTEX_NAMESPACE . '-settings', 'rtl', 'replace' );

		// Preload the settings so that the settings page can be rendered without waiting for the requests
		$preload_data = array_reduce(
			array(
				'/wp/v2/settings',
				array( '/wp/v2/settings', 'OPTIONS' ),
			),
			'rest_preload_api_request',
			array()
		);
		wp_add_inline_script(
			'wp-api-fetch',
			sprintf( 'wp.apiFetch.use( wp.apiFetch.createPreloadingMiddleware( %s ) );', wp_json_encode( $preload_data ) ),
			'after'
		);
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
		$settings = Options::get_settings();
		$css      = '';

		// Generate highlighter style
		foreach ( $settings['highlighter'] as $i => $highlighter ) {
			if ( $highlighter['active'] ) {
				$css_selector = ".rtex-highlighter-{$i}";
				$thickness    = $highlighter['thickness'];
				$color_hex    = $highlighter['color'];
				$type         = $highlighter['type'];
				$opacity      = $highlighter['opacity'] / 100;

				// Generate rgba value
				if ( 1 === $opacity ) {
					$color = $color_hex;
				} else {
					$r     = hexdec( substr( $color_hex, 1, 2 ) );
					$g     = hexdec( substr( $color_hex, 3, 2 ) );
					$b     = hexdec( substr( $color_hex, 5, 2 ) );
					$color = "rgba({$r}, {$g}, {$b}, {$opacity})";
				}

				// Generate gradient value
				if ( 'solid' === $type ) {
					$thickness = 100 - $thickness;
					if ( 0 === $thickness ) {
						$background_value = $color;
					} else {
						$background_value = "linear-gradient(transparent {$thickness}%, {$color} {$thickness}%)";
					}
				} elseif ( 'stripe' === $type ) {
					$background_value = "repeating-linear-gradient(-45deg, {$color} 0, {$color} 3px, transparent 3px, transparent 6px) no-repeat bottom/100% {$thickness}%";
				} elseif ( 'stripe-thin' === $type ) {
					$background_value = "repeating-linear-gradient(-45deg, {$color} 0, {$color} 2px, transparent 2px, transparent 4px) no-repeat bottom/100% {$thickness}%";
				}

				// Generate CSS
				$css .= "$css_selector{background: $background_value;}";
			}
		}

		// Generate font size style
		foreach ( $settings['font_size'] as $i => $font_size ) {
			if ( $font_size['active'] ) {
				$css .= ".rtex-font-size-{$i}{ font-size: {$font_size['size']};}";
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
		$settings = Options::get_settings();
		$config   = array(
			'highlighter' => array(),
			'fontSize'    => array(),
		);

		foreach ( $settings['highlighter'] as $i => $highlighter ) {
			if ( $highlighter['active'] ) {
				$config['highlighter'][] = array(
					'title'     => $highlighter['title'],
					'className' => 'rtex-highlighter-' . $i,
				);
			}
		}

		foreach ( $settings['font_size'] as $i => $font_size ) {
			if ( $font_size['active'] ) {
				$config['fontSize'][] = array(
					'title'     => $font_size['title'],
					'className' => 'rtex-font-size-' . $i,
					'size'      => $font_size['size'],
				);
			}
		}

		$config['underlineActive']   = $settings['underline_active'];
		$config['clearFormatActive'] = $settings['clear_format_active'];

		return $config;
	}
}

new Enqueue();
