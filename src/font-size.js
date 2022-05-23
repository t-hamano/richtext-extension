/**
 * WordPress dependencies
 */
import { registerFormatType } from '@wordpress/rich-text';
import { __ } from '@wordpress/i18n';
import { textColor as icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { getRichTextSetting } from './helper';

const label = __( 'Font size', 'richtext-extension' );
const slotFillName = 'FontSizeDropdownControls';

rtexConf.fontSize.forEach( ( { title, className }, index ) => {
	const setting = getRichTextSetting( { label, icon, title, className, slotFillName }, index );
	registerFormatType( ...setting );
} );
