/**
 * WordPress dependencies
 */
import {
	applyFormat,
	getActiveFormat,
	getActiveFormats,
	isCollapsed,
	removeFormat,
} from '@wordpress/rich-text';
import type { RichTextValue } from '@wordpress/rich-text';

/**
 * Toggles a format so that only one format of its group is applied at a time.
 *
 * When the format is applied, the other formats of the group are removed from
 * the selection first.
 *
 * @param {RichTextValue} value       Value to modify.
 * @param {string}        formatName  Format name to toggle.
 * @param {string[]}      formatNames Format names of the group.
 * @return {RichTextValue} A new value with the format applied or removed.
 */
export const toggleExclusiveFormat = (
	value: RichTextValue,
	formatName: string,
	formatNames: string[]
): RichTextValue => {
	if ( getActiveFormat( value, formatName ) ) {
		return removeFormat( value, formatName );
	}

	const otherFormatNames = formatNames.filter( ( name ) => name !== formatName );
	let newValue = value;

	if ( isCollapsed( value ) ) {
		// `removeFormat` would strip the format from the whole surrounding text
		// when the selection is collapsed, so only drop it from the formats used
		// for the next input, as `applyFormat` does.
		newValue = {
			...value,
			activeFormats: getActiveFormats( value ).filter(
				( { type } ) => ! otherFormatNames.includes( type )
			),
		} as RichTextValue;
	} else {
		otherFormatNames.forEach( ( name ) => {
			newValue = removeFormat( newValue, name );
		} );
	}

	return applyFormat( newValue, { type: formatName } );
};
