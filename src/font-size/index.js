/**
 * WordPress dependencies
 */
import { registerFormatType } from '@wordpress/rich-text';
import { getRichTextSetting } from './utils';

rtexConf.fontSize.forEach( ( { title, formatName, className, setting = {} }, index ) =>
	registerFormatType( ...getRichTextSetting( { title, formatName, className, setting }, index ) )
);
