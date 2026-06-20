/**
 * WordPress dependencies
 */
import type { registerFormatType, RichTextValue } from '@wordpress/rich-text';

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

/**
 * The bundled `WPFormat` type marks `interactive`, `object` and `name` as
 * required and omits `attributes`, none of which matches the runtime API of
 * `registerFormatType`, so settings are cast to this type when registering.
 */
export type FormatTypeSettings = Parameters< typeof registerFormatType >[ 1 ];
