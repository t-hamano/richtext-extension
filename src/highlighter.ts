/**
 * WordPress dependencies
 */
import { registerFormatType } from '@wordpress/rich-text';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { getRichTextSetting } from './helper';
import { adminAppearance as icon } from './icons';

const label = __( 'Highlighter', 'richtext-extension' );
const slotFillName = 'HighlighterDropdownControls';

rtexConf.highlighter.forEach( ( { title, className }, index ) => {
	const setting = getRichTextSetting( { label, icon, title, className, slotFillName }, index );
	registerFormatType( ...setting );
} );
