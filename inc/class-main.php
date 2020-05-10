<?php
/**
 * @package richtext-extension
 * @author Tetsuaki Hamano
 * @license GPL-2.0+
 */

namespace richtext_extension;

class Main {
	/**
	 * Constructor
	 */
	public function __construct() {
		// Load translated strings
		load_plugin_textdomain( 'richtext-extension', false, dirname( RTEX_BASENAME ) . '/languages' );

		// Uninstallation process
		register_uninstall_hook( RTEX_BASENAME, array( $this, 'uninstall_richtext_extension' ) );

		// Add a Link to this plugin settings page in plugin list
		add_filter( 'plugin_action_links_' . RTEX_BASENAME, array( $this, 'add_action_links' ) );

		// Load classes
		$this->load_classes();
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
