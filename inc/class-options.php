<?php
/**
 * @package richtext-extension
 * @author Tetsuaki Hamano
 * @license GPL-2.0+
 */

namespace richtext_extension;

class Options {
	// Settable font size range
	const MIN_FONT_SIZE = 80;
	const MAX_FONT_SIZE = 300;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add option page
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		// Create setting
		add_action( 'admin_init', array( $this, 'register_option' ) );
	}

	/**
	 * Add option page
	 */
	public function add_options_page() {
		$this->hook = add_options_page(
			__( 'RichText Extension Setting', 'richtext-extension' ),
			__( 'RichText Extension', 'richtext-extension' ),
			'manage_options',
			'richtext-extension-option',
			array( $this, 'create_options_page' )
		);

		//Load javascript to allow drag/drop, expand/collapse of metaboxes
		add_action( 'load-' . $this->hook, array( $this, 'load_postbox' ) );
	}

	/**
	 * Load javascript to allow drag/drop, expand/collapse of metaboxes
	 */
	public function load_postbox() {
		wp_enqueue_script( 'postbox' );
	}

		/**
	 * Create setting
	 */
	public function register_option() {
		for ( $i = 0; $i <= 3; $i++ ) {
			register_setting(
				'richtext-extension-group',
				'rtex_highlighter_active_' . $i,
				array( $this, 'sanitize_checkbox' )
			);
			register_setting(
				'richtext-extension-group',
				'rtex_highlighter_title_' . $i,
				'sanitize_text_field'
			);
			register_setting(
				'richtext-extension-group',
				'rtex_highlighter_color_' . $i,
				'sanitize_hex_color'
			);
			register_setting(
				'richtext-extension-group',
				'rtex_highlighter_thickness_' . $i,
				array( $this, 'sanitize_range' )
			);
			register_setting(
				'richtext-extension-group',
				'rtex_highlighter_opacity_' . $i,
				array( $this, 'sanitize_range' )
			);
		}

		for ( $i = 0; $i <= 3; $i++ ) {
			register_setting(
				'richtext-extension-group',
				'rtex_font_size_active_' . $i,
				array( $this, 'sanitize_checkbox' )
			);
			register_setting(
				'richtext-extension-group',
				'rtex_font_size_title_' . $i,
				'sanitize_text_field'
			);
			register_setting(
				'richtext-extension-group',
				'rtex_font_size_size_' . $i,
				array( $this, 'sanitize_font_size' )
			);
		}

		register_setting(
			'richtext-extension-group',
			'rtex_underline_active',
			array( $this, 'sanitize_checkbox' )
		);

		register_setting(
			'richtext-extension-group',
			'rtex_clear_format_active',
			array( $this, 'sanitize_checkbox' )
		);
	}

	/**
	 * Sanitizer (Chechbox)
	 * @param string $value input value.
	 *
	 * @return string
	 */
	public static function sanitize_checkbox( $value ) {
		return ( isset( $value ) ? true : false );
	}

	/**
	 * Sanitizer (Range)
	 * @param string $value input value.
	 *
	 * @return string
	 */
	public static function sanitize_range( $value ) {
		$value = absint( $value );
		$value = max( 0, $value );
		$value = min( 100, $value );
		return $value;
	}

		/**
	 * Sanitizer (Font size)
	 * @param string $value input value.
	 *
	 * @return string
	 */
	public static function sanitize_font_size( $value ) {
		$value = absint( $value );
		$value = max( self::MIN_FONT_SIZE, $value );
		$value = min( self::MAX_FONT_SIZE, $value );
		return $value;
	}

	/**
	 * Create meta boxes
	 */
	public function create_meta_boxes() {
		add_meta_box(
			'rtex-metabox-highlighter',
			__( 'Highlighter', 'richtext-extension' ),
			array( $this, 'metabox_highlighter' ),
			$this->hook,
			'normal'
		);
		add_meta_box(
			'rtex-metabox-font-size',
			__( 'Font size', 'richtext-extension' ),
			array( $this, 'metabox_font_size' ),
			$this->hook,
			'normal'
		);
		add_meta_box(
			'rtex-metabox-underline',
			__( 'Underline', 'richtext-extension' ),
			array( $this, 'metabox_underline' ),
			$this->hook,
			'normal'
		);
		add_meta_box(
			'rtex-metabox-clear-format',
			__( 'Clear format', 'richtext-extension' ),
			array( $this, 'metabox_clear_format' ),
			$this->hook,
			'normal'
		);
	}

	/**
	 * Create option page
	 */
	public function create_options_page() {
		self::create_meta_boxes();
		?>
		<div class="wrap">
			<h1><?php _e( 'RichText Extension Setting', 'richtext-extension' ); ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'richtext-extension-group' );
					do_settings_sections( 'richtext-extension-group' );
					wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
					wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				?>
				<div id="poststuff">
					<div id="post-body">
						<div id="post-body-content">
							<?php do_meta_boxes( $this->hook, 'normal', $data ); ?>
							<?php submit_button(); ?>
						</div>
					</div>
				</div>
			</form>
		</div>
		<script type="text/javascript">
			jQuery(document).ready( function( $ ) {
				// Meta box
				$( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );
				postboxes.add_postbox_toggles( '<?php echo $this->hook; ?>' );

				// Colorpicker
				$( '.rtex-colorpicker' ).wpColorPicker({
					change: function (event, ui) {
						var index = $( this ).parents( 'tr' ).attr( 'data-index' );
						var color = ui.color.toString();
						preview_highlighter( index, color );
					},
					clear: function (event) {
						var index = $( this ).parents( 'tr' ).attr( 'data-index' );
						var color = '';
						preview_highlighter( index, color );
					}
				});

				// Sync range and number input
				$( '.rtex-range [type="range"]' ).on( 'input', function() {
					var parent = $( this ).parents( '.rtex-range' );
					parent.find( '[type="number"]' ).val( $( this ).val() );
				} );
				$( '.rtex-range [type="number"]' ).on( 'change', function() {
					var parent = $( this ).parents( '.rtex-range' );
					parent.find( '[type="range"]' ).val( $( this ).val() );
				} );

				// Preview styles (Highlighter)
				$( '#rtex-metabox-highlighter input' ).on( 'change', function() {
					var index = $( this ).parents( 'tr' ).attr( 'data-index' );
					var color = $( '[name="rtex_highlighter_color_' + index + '"]').val();
					preview_highlighter( index, color );
				} );

				/**
				* Generate highlighter preview
				* @param  {string} index  Target Row
				* @param  {string} color Hex Color Code
				*/
				function preview_highlighter( index, color ) {
					var thickness = 100 - parseInt( $( '[name="rtex_highlighter_thickness_' + index + '"]').val() );
					var colorRgba = 'transparent';

					if( color.match( '^#[0-9a-fA-F]{6}$' ) ) {
						var r = parseInt( color.substr( 1, 2 ), 16 );
						var g = parseInt( color.substr( 3, 2 ), 16 );
						var b = parseInt( color.substr( 5, 2 ), 16 );
						var opacity = parseInt( $( '[name="rtex_highlighter_opacity_' + index + '"]' ).val() ) / 100;
						var colorRgba ='rgba(' + r + ',' + g + ',' + b + ',' + opacity + ')';
					}

					$( '#rtex-highlighter-preview-' + index ).css( 'background', 'linear-gradient(transparent ' + thickness + '%,' + colorRgba + ' ' + thickness + '%)' );
				}

				// Preview styles (Font size)
				$( '#rtex-metabox-font-size input' ).on( 'change', function() {
					var index = $( this ).parents( 'tr' ).attr( 'data-index' );
					var fontSize = parseInt( $( '[name="rtex_font_size_' + index + '"]').val() ) / 100;
					$( '#rtex-font-size-preview-' + index ).css( 'font-size', fontSize + 'em' );
				} );
			});
		</script>
		<?php
	}

	/**
	 * Meta box(Highlighter)
	 */
	public function metabox_highlighter() {
		$default_title = array(
			__( 'Marker (Yellow)', 'richtext-extension' ),
			__( 'Marker (Red)', 'richtext-extension' ),
			__( 'Background (Yellow)', 'richtext-extension' ),
			__( 'Background (Red)', 'richtext-extension' ),
		);
		?>
		<div class="rtex-inside">
			<ul>
				<li><?php _e( 'If the highlighter makes it hard to see the text, lower the opacity.', 'richtext-extension' ); ?></li>
				<li><?php _e( 'If you change each setting, the style you\'re already applying to your content will also change.', 'richtext-extension' ); ?></li>
			</ul>
			<div class="rtex-table-wrap">
				<table class="form-table rtex-table">
					<thead>
						<tr>
							<th style="width: 10%;"><?php _e( 'Status', 'richtext-extension' ); ?></th>
							<th style="width: 15%;"><?php _e( 'Title', 'richtext-extension' ); ?></th>
							<th style="width: 15%;"><?php _e( 'Color', 'richtext-extension' ); ?></th>
							<th style="width: 20%;"><?php _e( 'Thickness', 'richtext-extension' ); ?></th>
							<th style="width: 20%;"><?php _e( 'Opacity', 'richtext-extension' ); ?></th>
							<th style="width: 20%;"><?php _e( 'Preview', 'richtext-extension' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						for ( $i = 0; $i <= 3; $i++ ) :
							?>
							<tr data-index="<?php echo $i; ?>">
								<td>
									<label class="rtex-switch">
										<input id="<?php echo 'rtex_highlighter_active_' . $i; ?>" class="rtex-ui-button" type="checkbox" name="<?php echo 'rtex_highlighter_active_' . $i; ?>" value="1" <?php checked( get_option( 'rtex_highlighter_active_' . $i, true ) ); ?>>
										<span class="rtex-switch-thumb"></span>
										<span class="rtex-switch-track"></span>
									</label>
								</td>
								<td>
									<input type="text" name="<?php echo 'rtex_highlighter_title_' . $i; ?>" value="<?php echo get_option( 'rtex_highlighter_title_' . $i, $default_title[ $i ] ); ?>">
								</td>
								<td>
									<input type="text" name="<?php echo 'rtex_highlighter_color_' . $i; ?>" class="rtex-colorpicker" value="<?php echo get_option( 'rtex_highlighter_color_' . $i, $rtex_config['highlighter'][ $i ]['color'] ); ?>">
								</td>
								<td>
									<div class="rtex-range">
										<input type="range" min="0" max="100" step="1" value="<?php echo get_option( 'rtex_highlighter_thickness_' . $i, $rtex_config['highlighter'][ $i ]['thickness'] ); ?>">
										<div class="rtex-input">
											<input type="number" min="0" max="100" step="1" class="rtex-is-append" name="<?php echo 'rtex_highlighter_thickness_' . $i; ?>" value="<?php echo get_option( 'rtex_highlighter_thickness_' . $i, $rtex_config['highlighter'][ $i ]['thickness'] ); ?>">
											<span class="rtex-input-append">%</span>
										</div>
									</div>
								</td>
								<td>
									<div class="rtex-range">
										<input type="range" min="0" max="100" step="1" value="<?php echo get_option( 'rtex_highlighter_opacity_' . $i, $rtex_config['highlighter'][ $i ]['opacity'] ); ?>">
										<div class="rtex-input">
											<input type="number" min="0" max="100" step="1" class="rtex-is-append" name="<?php echo 'rtex_highlighter_opacity_' . $i; ?>" value="<?php echo get_option( 'rtex_highlighter_opacity_' . $i, $rtex_config['highlighter'][ $i ]['opacity'] ); ?>">
											<span class="rtex-input-append">%</span>
										</div>
									</div>
								</td>
								<td>
									<span id="rtex-highlighter-preview-<?php echo $i; ?>"><?php _e( 'Hello World!', 'richtext-extension' ); ?></span>
								</td>
							</tr>
						<?php endfor; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	/**
	 * Meta box(Font size)
	 */
	public function metabox_font_size() {
		$default_title = array(
			__( 'Extra small', 'richtext-extension' ),
			__( 'Small', 'richtext-extension' ),
			__( 'Large', 'richtext-extension' ),
			__( 'Extra large', 'richtext-extension' ),
		);
		?>
		<div class="rtex-inside">
			<ul>
				<li><?php _e( 'The size is specified as a percentage of the base font size.', 'richtext-extension' ); ?></li>
				<li><?php _e( 'If you change each setting, the style you\'re already applying to your content will also change.', 'richtext-extension' ); ?></li>
			</ul>
			<div class="rtex-table-wrap">
				<table class="form-table rtex-table">
					<thead>
						<tr>
							<th style="width: 10%;"><?php _e( 'Status', 'richtext-extension' ); ?></th>
							<th style="width: 15%;"><?php _e( 'Title', 'richtext-extension' ); ?></th>
							<th style="width: 20%;"><?php _e( 'Size', 'richtext-extension' ); ?></th>
							<th style="width: 55%;"><?php _e( 'Preview', 'richtext-extension' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						for ( $i = 0; $i <= 3; $i++ ) :
							?>
							<tr data-index="<?php echo $i; ?>">
								<td>
									<label class="rtex-switch">
										<input id="<?php echo 'rtex_font_size_active_' . $i; ?>" class="rtex-ui-button" type="checkbox" name="<?php echo 'rtex_font_size_active_' . $i; ?>" value="1" <?php checked( get_option( 'rtex_font_size_active_' . $i, true ) ); ?>>
										<span class="rtex-switch-thumb"></span>
										<span class="rtex-switch-track"></span>
									</label>
								</td>
								<td>
									<input type="text" name="<?php echo 'rtex_font_size_title_' . $i; ?>" value="<?php echo get_option( 'rtex_font_size_title_' . $i, $default_title[ $i ] ); ?>">
								</td>
								<td>
									<div class="rtex-range">
										<input type="range" min="<?php echo self::MIN_FONT_SIZE; ?>" max="<?php echo self::MAX_FONT_SIZE; ?>" step="1" value="<?php echo get_option( 'rtex_font_size_size_' . $i, $rtex_config['font_size'][ $i ] ); ?>">
										<div class="rtex-input">
											<input type="number" min="<?php echo self::MIN_FONT_SIZE; ?>" max="<?php echo self::MAX_FONT_SIZE; ?>" step="1" class="rtex-is-append" name="<?php echo 'rtex_font_size_size_' . $i; ?>"value="<?php echo get_option( 'rtex_font_size_size_' . $i, $rtex_config['font_size'][ $i ] ); ?>">
											<span class="rtex-input-append">%</span>
										</div>
									</div>
								</td>
								<td>
									<?php _e( 'Hello World!', 'richtext-extension' ); ?><span id="rtex-font-size-preview-<?php echo $i; ?>"> <?php _e( 'Hello This World!', 'richtext-extension' ); ?></span> <?php _e( 'Hello World!', 'richtext-extension' ); ?>
								</td>
							</tr>
						<?php endfor; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	/**
	 * Meta box(Underline)
	 */
	public function metabox_underline() {
		?>
		<div class="rtex-inside">
			<label>
				<input class="rtex-ui-button" type="checkbox" name="<?php echo 'rtex_underline_active'; ?>" value="1" <?php checked( get_option( 'rtex_underline_active', true ) ); ?>><?php _e( 'Enable', 'richtext-extension' ); ?>
			</label>
		</div>
		<?php
	}

		/**
	 * Meta box(Clear format)
	 */
	public function metabox_clear_format() {
		?>
		<div class="rtex-inside">
			<label>
				<input class="rtex-ui-button" type="checkbox" name="<?php echo 'rtex_clear_format_active'; ?>" value="1" <?php checked( get_option( 'rtex_clear_format_active', true ) ); ?>><?php _e( 'Enable', 'richtext-extension' ); ?>
			</label>
		</div>
		<?php
	}
}

new Options();
