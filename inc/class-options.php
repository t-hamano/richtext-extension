<?php
/**
 * @package richtext-extension
 * @author Aki Hamano
 * @license GPL-2.0+
 */

namespace richtext_extension;

class Options {
	/**
	 * Settable font size range
	 */
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
		// Register settings
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
			register_setting(
				'richtext-extension-group',
				'rtex_highlighter_type_' . $i,
				array( $this, 'sanitize_highlighter_type' )
			);
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
	public static function sanitize_checkbox( $input ) {
		return ( 1 === (int) $input ) ? 1 : 0;
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
	 * Sanitizer (Highlighter type)
	 * @param string $value input value.
	 *
	 * @return string
	 */
	public static function sanitize_highlighter_type( $value ) {
		$allowed_types = array( 'solid', 'stripe', 'stripe-thin' );
		return in_array( $value, $allowed_types, true ) ? $value : 'solid';
	}

	/**
	 * Create meta boxes
	 */
	public function create_meta_boxes() {
		$meta_boxes = array(
			array(
				'slug'  => 'highlighter',
				'label' => __( 'Highlighter', 'richtext-extension' ),
			),
			array(
				'slug'  => 'font_size',
				'label' => __( 'Font size', 'richtext-extension' ),
			),
			array(
				'slug'  => 'underline',
				'label' => __( 'Underline', 'richtext-extension' ),
			),
			array(
				'slug'  => 'clear_format',
				'label' => __( 'Clear format', 'richtext-extension' ),
			),
		);

		foreach ( $meta_boxes as $meta_box ) {
			add_meta_box(
				'rtex-metabox-' . $meta_box['slug'],
				$meta_box['label'],
				array( $this, 'metabox_' . $meta_box['slug'] ),
				$this->hook,
				'normal'
			);
		}
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
				<div class="rtex-wrapper">
					<div id="poststuff">
						<div id="post-body">
							<div id="post-body-content">
								<?php do_meta_boxes( $this->hook, 'normal', null ); ?>
								<?php submit_button(); ?>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<script type="text/javascript">
		jQuery( document ).ready( function ( $ ) {
			// Apply styles to previews when the document is loaded
			$( '#rtex-table-body-highlighter tr' ).each( function ( index ) {
				previewHighlighter( index );
			} );

			$( '#rtex-table-body-font-size tr' ).each( function ( index ) {
				previewFontSize( index );
			} );

			// Meta box
			$( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );
			postboxes.add_postbox_toggles( '<?php echo $this->hook; ?>' );

			// Colorpicker
			$( '.rtex-colorpicker' ).wpColorPicker( {
				change( event, ui ) {
					const index = $( this ).parents( 'tr' ).attr( 'data-index' );
					const color = ui.color.toString();
					previewHighlighter( index, color );
				},
				clear() {
					const index = $( this ).parents( 'tr' ).attr( 'data-index' );
					previewHighlighter( index );
				},
			} );

			// Sync range and number input
			$( '.rtex-range [type="range"]' ).on( 'input', function () {
				const parent = $( this ).parents( '.rtex-range' );
				parent.find( '[type="number"]' ).val( $( this ).val() );
			} );
			$( '.rtex-range [type="number"]' ).on( 'change', function () {
				const parent = $( this ).parents( '.rtex-range' );
				parent.find( '[type="range"]' ).val( $( this ).val() );
			} );

			// Update highlighter preview styles
			$( '#rtex-metabox-highlighter input, #rtex-metabox-highlighter select' ).on( 'change', function () {
					const index = $( this ).parents( 'tr' ).attr( 'data-index' );
					previewHighlighter( index );
			} );

			// Update font size preview styles
			$( '#rtex-metabox-font-size input' ).on( 'change', function () {
					previewFontSize( index );
			} );

			/**
			 * Update highlighter preview styles
			 *
			 * @param {string}           index       Target row index
			 * @param {string|undefined} pickerColor Colorpicker value
			 */
			function previewHighlighter( index, pickerColor ) {
				const target = $( '#rtex-highlighter-preview-' + index );
				const thickness = parseInt( $( '[name="rtex_highlighter_thickness_' + index + '"]' ).val() );
				const colorHex = pickerColor || $( '[name="rtex_highlighter_color_' + index + '"]' ).val();
				const type = $( '[name="rtex_highlighter_type_' + index + '"]' ).val();
				let color = 'transparent';

				// Generate rgba value
				if ( colorHex.match( '^#[0-9a-fA-F]{6}$' ) ) {
					const opacity =
						parseInt( $( '[name="rtex_highlighter_opacity_' + index + '"]' ).val() ) / 100;
					if ( 1 === opacity ) {
						color = colorHex;
					} else {
						const r = parseInt( colorHex.substr( 1, 2 ), 16 );
						const g = parseInt( colorHex.substr( 3, 2 ), 16 );
						const b = parseInt( colorHex.substr( 5, 2 ), 16 );
						color = `rgba(${ r },${ g },${ b },${ opacity })`;
					}
				}

				// Apply gradient value
				if ( 'solid' === type ) {
					if ( 0 === thickness ) {
						target.css( 'background', color );
					} else {
						target.css(
							'background',
							`linear-gradient(transparent ${ 100 - thickness }%, ${ color } ${ 100 - thickness }%)`
						);
					}
				} else if ( 'stripe' === type ) {
					target.css(
						'background',
						`repeating-linear-gradient(-45deg, ${ color } 0, ${ color } 3px, transparent 3px, transparent 6px) no-repeat bottom/100% ${ thickness }%`
					);
				} else if ( 'stripe-thin' === type ) {
					target.css(
						'background',
						`repeating-linear-gradient(-45deg, ${ color } 0, ${ color } 2px, transparent 2px, transparent 4px) no-repeat bottom/100% ${ thickness }%`
					);
				}
			}

			/**
			 * Update font size preview styles
			 *
			 * @param {string} index Target row index
			 */
			function previewFontSize( index ) {
				const target = $( '#rtex-font-size-preview-' + index );
				const fontSize = parseInt( $( '[name="rtex_font_size_size_' + index + '"]' ).val() ) / 100;
				target.css( 'font-size', fontSize + 'em' );
			}
		} );
		</script>
		<?php
	}

	/**
	 * Meta box(Highlighter)
	 */
	public function metabox_highlighter() {
		$default_title = array(
			__( 'Solid Marker', 'richtext-extension' ),
			__( 'Striped Marker', 'richtext-extension' ),
			__( 'Solid Background', 'richtext-extension' ),
			__( 'Striped Background', 'richtext-extension' ),
		);

		$highlighter_types = array(
			array(
				'value' => 'solid',
				'label' => __( 'Solid', 'richtext-extension' ),
			),
			array(
				'value' => 'stripe',
				'label' => __( 'Stripe', 'richtext-extension' ),
			),
			array(
				'value' => 'stripe-thin',
				'label' => __( 'Stripe (Thin)', 'richtext-extension' ),
			),
		);

		?>
		<ul>
			<li><?php _e( 'If the highlighter makes it hard to see the text, lower the opacity.', 'richtext-extension' ); ?></li>
			<li><?php _e( 'If you change each setting, the style you\'re already applying to your content will also change.', 'richtext-extension' ); ?></li>
		</ul>
		<div class="rtex-table-wrap">
			<table class="form-table rtex-table">
				<thead>
					<tr>
						<th ><?php _e( 'Status', 'richtext-extension' ); ?></th>
						<th><?php _e( 'Title', 'richtext-extension' ); ?></th>
						<th><?php _e( 'Color', 'richtext-extension' ); ?></th>
						<th><?php _e( 'Thickness', 'richtext-extension' ); ?></th>
						<th><?php _e( 'Opacity', 'richtext-extension' ); ?></th>
						<th><?php _e( 'Type', 'richtext-extension' ); ?></th>
						<th><?php _e( 'Preview', 'richtext-extension' ); ?></th>
					</tr>
				</thead>
				<tbody id="rtex-table-body-highlighter">
					<?php
					for ( $i = 0; $i <= 3; $i++ ) :
						$is_active = get_option( 'rtex_highlighter_active_' . $i, true );
						$title     = get_option( 'rtex_highlighter_title_' . $i, $default_title[ $i ] );
						$color     = get_option( 'rtex_highlighter_color_' . $i, Config::$highlighter[ $i ]['color'] );
						$thickness = get_option( 'rtex_highlighter_thickness_' . $i, Config::$highlighter[ $i ]['thickness'] );
						$opacity   = get_option( 'rtex_highlighter_opacity_' . $i, Config::$highlighter[ $i ]['opacity'] );
						$type      = get_option( 'rtex_highlighter_type_' . $i, Config::$highlighter[ $i ]['type'] );
						?>
						<tr data-index="<?php echo $i; ?>">
							<td>
								<label class="rtex-switch">
									<input id="<?php echo 'rtex_highlighter_active_' . $i; ?>" class="rtex-ui-button" type="checkbox" name="<?php echo 'rtex_highlighter_active_' . $i; ?>" value="1" <?php checked( $is_active ); ?>>
									<span class="rtex-switch-thumb"></span>
									<span class="rtex-switch-track"></span>
								</label>
							</td>
							<td>
								<input type="text" name="<?php echo 'rtex_highlighter_title_' . $i; ?>" value="<?php echo esc_attr( $title ); ?>">
							</td>
							<td>
								<input type="text" name="<?php echo 'rtex_highlighter_color_' . $i; ?>" class="rtex-colorpicker" value="<?php echo esc_attr( $color ); ?>">
							</td>
							<td>
								<div class="rtex-range">
									<input type="range" min="0" max="100" step="1" value="<?php echo esc_attr( $thickness ); ?>">
									<div class="rtex-input">
										<input type="number" min="0" max="100" step="1" name="<?php echo 'rtex_highlighter_thickness_' . $i; ?>" value="<?php echo esc_attr( $thickness ); ?>">
										<span>%</span>
									</div>
								</div>
							</td>
							<td>
								<div class="rtex-range">
									<input type="range" min="0" max="100" step="1" value="<?php echo esc_attr( $opacity ); ?>">
									<div class="rtex-input">
										<input type="number" min="0" max="100" step="1" name="<?php echo 'rtex_highlighter_opacity_' . $i; ?>" value="<?php echo esc_attr( $opacity ); ?>">
										<span>%</span>
									</div>
								</div>
							</td>
							<td>
								<select name="<?php echo 'rtex_highlighter_type_' . $i; ?>">
									<?php
									foreach ( $highlighter_types as $highlighter_type ) {
										if ( $type === $highlighter_type['value'] ) {
											echo '<option selected="selected" value="' . esc_attr( $highlighter_type['value'] ) . '">' . $highlighter_type['label'] . '</option>';
										} else {
											echo '<option value="' . esc_attr( $highlighter_type['value'] ) . '">' . $highlighter_type['label'] . '</option>';
										}
									}
									?>
								</select>
							</td>
							<td>
								<span id="rtex-highlighter-preview-<?php echo $i; ?>"><?php _e( 'Hello World !', 'richtext-extension' ); ?></span>
							</td>
						</tr>
					<?php endfor; ?>
				</tbody>
			</table>
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
				<tbody id="rtex-table-body-font-size">
					<?php
					for ( $i = 0; $i <= 3; $i++ ) :
						$is_active = get_option( 'rtex_font_size_active_' . $i, true );
						$title     = get_option( 'rtex_font_size_title_' . $i, $default_title[ $i ] );
						$size      = get_option( 'rtex_font_size_size_' . $i, Config::$font_size[ $i ] );
						?>
						<tr data-index="<?php echo $i; ?>">
							<td>
								<label class="rtex-switch">
									<input id="<?php echo 'rtex_font_size_active_' . $i; ?>" class="rtex-ui-button" type="checkbox" name="<?php echo 'rtex_font_size_active_' . $i; ?>" value="1" <?php checked( $is_active ); ?>>
									<span class="rtex-switch-thumb"></span>
									<span class="rtex-switch-track"></span>
								</label>
							</td>
							<td>
								<input type="text" name="<?php echo 'rtex_font_size_title_' . $i; ?>" value="<?php echo esc_attr( $title ); ?>">
							</td>
							<td>
								<div class="rtex-range">
									<input type="range" min="<?php echo self::MIN_FONT_SIZE; ?>" max="<?php echo self::MAX_FONT_SIZE; ?>" step="1" value="<?php echo esc_attr( $size ); ?>">
									<div class="rtex-input">
										<input type="number" min="<?php echo self::MIN_FONT_SIZE; ?>" max="<?php echo self::MAX_FONT_SIZE; ?>" step="1" name="<?php echo 'rtex_font_size_size_' . $i; ?>"value="<?php echo esc_attr( $size ); ?>">
										<span>%</span>
									</div>
								</div>
							</td>
							<td>
								<?php _e( 'Hello World !', 'richtext-extension' ); ?><span id="rtex-font-size-preview-<?php echo $i; ?>"> <?php _e( 'Hello This World !', 'richtext-extension' ); ?></span> <?php _e( 'Hello World !', 'richtext-extension' ); ?>
							</td>
						</tr>
					<?php endfor; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Meta box(Underline)
	 */
	public function metabox_underline() {
		?>
		<label>
			<input class="rtex-ui-button" type="checkbox" name="<?php echo 'rtex_underline_active'; ?>" value="1" <?php checked( get_option( 'rtex_underline_active', true ) ); ?>><?php _e( 'Enable', 'richtext-extension' ); ?>
		</label>
		<p><strong><?php _e( 'Note: The underline specifications have changed from version 2.0.0. Try clearing the format if existing underlines do not work.', 'richtext-extension' ); ?></strong></p>
		<?php
	}

	/**
	 * Meta box(Clear format)
	 */
	public function metabox_clear_format() {
		?>
		<label>
			<input class="rtex-ui-button" type="checkbox" name="<?php echo 'rtex_clear_format_active'; ?>" value="1" <?php checked( get_option( 'rtex_clear_format_active', true ) ); ?>><?php _e( 'Enable', 'richtext-extension' ); ?>
		</label>
		<?php
	}
}

new Options();
