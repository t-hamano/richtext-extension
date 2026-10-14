/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { textColor as icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { registerFormatDropdown } from './register-format-dropdown';

registerFormatDropdown( rtexConf.fontSize, {
	label: __( 'Font size', 'richtext-extension' ),
	icon,
	renderItemLabel: ( item ) =>
		sprintf(
			/* translators: 1: Font size title. 2: Font size with a unit, e.g. 1.3em. */
			__( '%1$s (%2$s)', 'richtext-extension' ),
			item.title,
			item.size
		),
} );
