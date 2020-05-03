<?php
/**
 * @package richtext-extension
 * @author Tetsuaki Hamano
 * @license GPL-2.0+
 */

namespace richtext_extension;

class Main {
	// Environment required for this plugin
	const REQ_WP_VERSION  = '5.4';
	const REQ_PHP_VERSION = '7.3';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Load translated strings
		load_plugin_textdomain( 'richtext-extension', false, dirname( RTEX_BASENAME ) . '/languages' );

		// Check the environment required for the plugin
		register_activation_hook( RTEX_BASENAME, array( $this, 'activation_check' ) );

		// Uninstallation process
		register_uninstall_hook( RTEX_BASENAME, array( $this, 'uninstall_richtext_extension' ) );

		// Add a Link to this plugin settings page in plugin list
		add_filter( 'plugin_action_links_' . RTEX_BASENAME, array( $this, 'add_action_links' ) );

		// Load classes
		$this->load_classes();
	}

	/**
	 * Check the environment required for the plugin
	 */
	public function activation_check() {
		global $wp_version;
		$php_version = phpversion();

		if ( version_compare( $php_version, self::REQ_PHP_VERSION, '<' ) ) {
			deactivate_plugins( RTEX_BASENAME );
			wp_die(
				sprintf(
					// translators: %1$s: required PHP version, %2$s: PHP version on this site
					__( '<p>Sorry, RichText Extension requires PHP %1$s or later (PHP version on this site: %2$s).</p>', 'richtext-extension' ),
					self::REQ_PHP_VERSION,
					$php_version
				)
			);
		} elseif ( version_compare( $wp_version, self::REQ_WP_VERSION, '<' ) ) {
			deactivate_plugins( BASENAME );
			wp_die(
				sprintf(
					// translators: %1$s: required WordPress version, %2$s: WordPress version on this site
					__( '<p>Sorry, RichText Extension requires WordPress %1$s or later (WordPress version on this site: %2$s).</p>', 'richtext-extension' ),
					self::REQ_WP_VERSION,
					$php_version
				)
			);
		}
	}

	/**
	 * Load classes
	 */
	private function load_classes() {
		require_once( RTEX_PATH . '/inc/class-config.php' );
		require_once( RTEX_PATH . '/inc/class-enqueue.php' );
		require_once( RTEX_PATH . '/inc/class-options.php' );
	}

	/**
	 * Add a Link to this plugin settings page in plugin list
	 */
	public function add_action_links( $links ) {
		$link = '<a href="' . admin_url( 'options-general.php?page=richtext-extension-option' ) . '">' . __( 'Settings', 'richtext-extension' ) . '</a>';
		array_unshift( $links, $link );
		return $links;
	}

	/**
	 * Uninstallation process
	 */
	public function uninstall_richtext_extension() {
		$options = array();

		for ( $i = 0; $i <= 3; $i ++ ) {
			$options[] = 'rtex_highlighter_active_' . $i;
			$options[] = 'rtex_highlighter_title_' . $i;
			$options[] = 'rtex_highlighter_color_' . $i;
			$options[] = 'rtex_highlighter_thickness_' . $i;
			$options[] = 'rtex_highlighter_opacity_' . $i;
		}

		for ( $i = 0; $i <= 3; $i ++ ) {
			$options[] = 'rtex_font_size_active_' . $i;
			$options[] = 'rtex_font_size_title_' . $i;
			$options[] = 'rtex_font_size_size_' . $i;
		}

		$options[] = 'rtex_underline_active';
		$options[] = 'rtex_clear_format_active';

		foreach ( $options as $key ) {
			delete_option( $key );
		}
	}
}
