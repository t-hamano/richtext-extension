<?php
/**
 * @package richtext-extension
 * @author Aki Hamano
 * @license GPL-2.0+
 */

namespace richtext_extension;

class Main {
	/**
	 * Constructor
	 */
	public function __construct() {
		// Add a Link to this plugin settings page in plugin list
		add_filter( 'plugin_action_links_' . RTEX_BASENAME, array( $this, 'add_action_links' ) );

		// Load classes
		$this->load_classes();
	}

	/**
	 * Load classes
	 */
	private function load_classes() {
		require_once RTEX_PATH . '/inc/class-config.php';
		require_once RTEX_PATH . '/inc/class-enqueue.php';
		require_once RTEX_PATH . '/inc/class-options.php';
	}

	/**
	 * Add a Link to this plugin settings page in plugin list
	 */
	public function add_action_links( $links ) {
		$link = '<a href="' . admin_url( 'options-general.php?page=richtext-extension-option' ) . '">' . __( 'Settings', 'richtext-extension' ) . '</a>';
		array_unshift( $links, $link );
		return $links;
	}
}
