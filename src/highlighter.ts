/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { getRichTextSetting } from './helper';
import { registerFormatType } from './register-format-type';
import { adminAppearance as icon } from './icons';

const label = __( 'Highlighter', 'richtext-extension' );
const slotFillName = 'HighlighterDropdownControls';

rtexConf.highlighter.forEach( ( { title, className }, index ) => {
	const setting = getRichTextSetting( { label, icon, title, className, slotFillName }, index );
	registerFormatType( ...setting );
} );
