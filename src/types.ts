/**
 * WordPress dependencies
 */
import type { RichTextValue } from '@wordpress/rich-text';

export type FormatEditProps = {
	isActive: boolean;
	value: RichTextValue;
	onChange: ( value: RichTextValue ) => void;
};

export type RichTextSettingArgs = {
	label: string;
	icon: JSX.Element;
	title: string;
	className: string;
	slotFillName: string;
};

export type FormatTypeSettings = {
	title: string;
	tagName: string;
	className: string | null;
	attributes?: Record< string, string >;
	edit?: ( props: FormatEditProps ) => JSX.Element;
};
