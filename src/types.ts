/**
 * WordPress dependencies
 */
import type { RichTextValue } from '@wordpress/rich-text';

/**
 * Internal dependencies
 */
import type { FontSizeSetting, FontSizeUnit, HighlighterSetting, Settings } from './settings/types';

export type FormatEditProps = {
	isActive: boolean;
	value: RichTextValue;
	onChange: ( value: RichTextValue ) => void;
};

export type FormatTypeSettings = {
	title: string;
	tagName: string;
	className: string | null;
	attributes?: Record< string, string >;
	edit?: ( props: FormatEditProps ) => React.JSX.Element;
};

/**
 * Settings passed to the block editor as `rtexConf`.
 */
export type EditorConfig = {
	highlighter: ( Pick< HighlighterSetting, 'title' > & { className: string } )[];
	fontSize: ( Pick< FontSizeSetting, 'title' | 'size' > & { className: string } )[];
	underlineActive: Settings[ 'underline_active' ];
	clearFormatActive: Settings[ 'clear_format_active' ];
};

/**
 * Settings passed to the settings page as `rtexSettingsConf`.
 */
export type SettingsPageConfig = {
	fontSizeRanges: Record< FontSizeUnit, [ number, number ] >;
};
