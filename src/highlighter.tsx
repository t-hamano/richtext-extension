/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { registerFormatDropdown } from './register-format-dropdown';
import { adminAppearance as icon } from './icons';

registerFormatDropdown( rtexConf.highlighter, {
	label: __( 'Highlighter', 'richtext-extension' ),
	icon,
	renderItemLabel: ( item ) => <span className={ item.className }>{ item.title }</span>,
} );
