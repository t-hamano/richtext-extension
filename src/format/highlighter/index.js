import { registerFormatType } from '@wordpress/rich-text';
import { getRichTextSetting } from './utils';
import { __ } from '@wordpress/i18n';

rtexConf.highlighter.forEach( ({ title, formatName, className, setting = {} }, index ) => registerFormatType( ...getRichTextSetting({ title, formatName, className, setting }, index ) ) );
